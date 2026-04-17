<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\TaskAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskStatusUpdated;
use App\Mail\TaskAssigned;
use App\Models\Project;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        
        $query = Task::where('company_id', $companyId)
            ->with(['assignedUser', 'creator', 'project', 'attachments.user']);

        // Solo mostrar tareas de proyectos a los que el usuario pertenece
        // Los roles super y admin pueden ver todas las tareas de la empresa
        if ($user->role !== 'super' && $user->role !== 'admin') {
            $query->whereHas('project.team', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });

            // Adicionalmente, los usuarios normales solo ven sus tareas asignadas
            if ($user->role === 'usuario') {
                $query->where('assigned_to', $user->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->latest()->paginate(10);
        $users = User::where('company_id', $companyId)->where('status', 'active')->get();
        $projects = $this->getAvailableProjects();

        return view('tasks.index', compact('tasks', 'users', 'projects'));
    }

    public function create()
    {
        $users = User::where('company_id', Auth::user()->company_id)
            ->where('status', 'active')->get();
        $projects = $this->getAvailableProjects();
        return view('tasks.create', compact('users', 'projects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:30720',
        ]);

        $data['company_id'] = Auth::user()->company_id;
        $data['created_by'] = Auth::id();
        $data['status'] = 'pending';

        $task = Task::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tasks/attachments', 'public');
                $user = Auth::user();
                $group = $user->isSupervisor() ? 'supervisor' : 'assigned';
                
                TaskAttachment::create([
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $this->getFileType($file),
                    'uploader_group' => $group,
                ]);
            }
        }

        if ($task->assigned_to) {
            $task->load('assignedUser');
            $email = User::find($task->assigned_to)->email;
            if ($task->assignedUser && $task->assignedUser->email) {
                Mail::to($email)->send(new TaskAssigned($task, $email));
            }
        }

        return redirect()->route('tasks.index')->with('success', 'Tarea creada exitosamente.');
    }

    public function edit(Task $task)
    {
        $this->authorizeCompany($task);
        $users = User::where('company_id', Auth::user()->company_id)
            ->where('status', 'active')->get();
        $projects = $this->getAvailableProjects();
        return view('tasks.edit', compact('task', 'users', 'projects'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeCompany($task);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
            'priority' => 'required|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date',
            'project_id' => 'required|exists:projects,id',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:30720',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tasks/attachments', 'public');
                $user = Auth::user();
                $group = $user->isSupervisor() ? 'supervisor' : 'assigned';

                TaskAttachment::create([
                    'task_id' => $task->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $this->getFileType($file),
                    'uploader_group' => $group,
                ]);
            }
        }

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada exitosamente.');
    }

    public function destroyAttachment(TaskAttachment $attachment)
    {
        $task = $attachment->task;
        $this->authorizeCompany($task);

        // Solo el supervisor o el dueño del archivo pueden borrarlo
        if (!Auth::user()->isSupervisor() && Auth::id() !== $attachment->user_id) {
            abort(403);
        }

        \Illuminate\Support\Facades\Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return back()->with('success', 'Archivo eliminado.');
    }

    private function getFileType($file)
    {
        $mime = $file->getMimeType();
        return str_contains($mime, 'video') ? 'video' : 'image';
    }

    public function updateStatus(Request $request, Task $task)
    {
        $this->authorizeCompany($task);

        $request->validate([
            'status' => 'required|in:pending,in_progress,review,completed,cancelled',
        ]);

        $task->update(['status' => $request->status]);

        if ($task->assigned_to) {
            $task->load('assignedUser');
            $email = User::find($task->assigned_to)->email;
            
            if ($task->assignedUser && $task->assignedUser->email) {
                Mail::to($email)->send(new TaskStatusUpdated($task, $email));
            }
        }

        return response()->json(['success' => true, 'message' => 'Estado actualizado.']);
    }

    public function destroy(Task $task)
    {
        $this->authorizeCompany($task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada exitosamente.');
    }

    private function getAvailableProjects()
    {
        $user = Auth::user();
        $query = Project::where('company_id', $user->company_id);

        if ($user->role !== 'super' && $user->role !== 'admin') {
            $query->whereHas('team', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }

        return $query->get();
    }

    private function authorizeCompany(Task $task)
    {
        if ($task->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
