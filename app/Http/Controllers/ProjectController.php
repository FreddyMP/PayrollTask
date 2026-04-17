<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Project::where('company_id', $user->company_id)->with(['creator', 'team']);

        if ($user->role === 'usuario') {
            $query->whereHas('team', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $projects = $query->latest()->paginate(15);
        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        $users = User::where('company_id', Auth::user()->company_id)
            ->whereIn('role', ['supervisor', 'usuario'])
            ->where('status', 'active')
            ->get();
            
        return view('projects.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,on_hold,completed,cancelled',
            'team' => 'nullable|array',
            'team.*' => 'exists:users,id'
        ]);

        $data['company_id'] = Auth::user()->company_id;
        $data['created_by'] = Auth::id();

        $project = Project::create($data);

        if ($request->filled('team')) {
            $project->team()->sync($request->team);
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto creado exitosamente.');
    }

    public function show(Project $project)
    {
        $this->authorizeProject($project);
        $project->load(['creator', 'team', 'tasks.assignedUser']);
        
        $availableUsers = collect();
        if (Auth::user()->isSupervisor()) {
            $availableUsers = User::where('company_id', Auth::user()->company_id)
                ->whereIn('role', ['supervisor', 'usuario'])
                ->where('status', 'active')
                ->get();
        }
        
        return view('projects.show', compact('project', 'availableUsers'));
    }

    public function updateTeam(Request $request, Project $project)
    {
        $this->authorizeAdmin();
        $this->authorizeProject($project);

        $request->validate([
            'team' => 'nullable|array',
            'team.*' => 'exists:users,id'
        ]);

        $project->team()->sync($request->team ?? []);

        return back()->with('success', 'Equipo del proyecto actualizado correctamente.');
    }

    public function edit(Project $project)
    {
        $this->authorizeAdmin();
        $this->authorizeProject($project);
        
        $users = User::where('company_id', Auth::user()->company_id)
            ->whereIn('role', ['supervisor', 'usuario'])
            ->where('status', 'active')
            ->get();
            
        $projectTeamIds = $project->team->pluck('id')->toArray();
            
        return view('projects.edit', compact('project', 'users', 'projectTeamIds'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeAdmin();
        $this->authorizeProject($project);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,on_hold,completed,cancelled',
            'team' => 'nullable|array',
            'team.*' => 'exists:users,id'
        ]);

        $project->update($data);

        if ($request->has('team')) {
            $project->team()->sync($request->team);
        } else {
            $project->team()->detach();
        }

        return redirect()->route('projects.index')->with('success', 'Proyecto actualizado exitosamente.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeAdmin();
        $this->authorizeProject($project);
        
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado exitosamente.');
    }

    private function authorizeAdmin()
    {
        if (!Auth::user()->isSupervisor()) {
            abort(403);
        }
    }

    private function authorizeProject(Project $project)
    {
        if ($project->company_id !== Auth::user()->company_id) {
            abort(403);
        }
        
        // Users can only see projects they belong to
        if (Auth::user()->role === 'usuario') {
            if (!$project->team()->where('user_id', Auth::id())->exists()) {
                abort(403);
            }
        }
    }
}
