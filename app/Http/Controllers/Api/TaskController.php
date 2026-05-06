<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the tasks for the authenticated user based on their role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Task::with(['project', 'creator', 'assignedUser']);

        if ($user->isSuper()) {
            // Super: ve todas las tareas de su empresa
            $query->where('company_id', $user->company_id);
        } elseif ($user->isSupervisor()) {
            // Supervisor: ve todas las tareas de los proyectos en los que está
            $projectIds = $user->projects()->pluck('projects.id');
            $query->whereIn('project_id', $projectIds);
        } else {
            // Usuario normal: solo sus tareas asignadas
            $query->where('assigned_to', $user->id);
        }
        
        $tasks = $query->latest()->get();

        return response()->json([
            'tasks' => $tasks,
            'count' => $tasks->count()
        ]);
    }
}
