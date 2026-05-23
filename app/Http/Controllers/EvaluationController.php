<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationAssignment;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use App\Models\Employee;
use App\Mail\EvaluationAssignedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::where('company_id', Auth::user()->company_id)->withCount('questions', 'assignments')->get();
        return view('evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        // Handled in a modal or separate view if needed, for simplicity we can use a modal in index
        return redirect()->route('evaluations.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $evaluation = Evaluation::create([
            'company_id' => Auth::user()->company_id,
            'title' => $request->title,
            'description' => $request->description,
            'allow_multiple_responses' => $request->has('allow_multiple_responses'),
            'status' => 'active',
        ]);

        return redirect()->route('evaluations.show', $evaluation)->with('success', 'Evaluación creada. Ahora añade las preguntas.');
    }

    public function show(Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $evaluation->load(['questions', 'assignments.employee.user']);
        $employees = Employee::where('company_id', Auth::user()->company_id)->with('user')->get();
        
        return view('evaluations.show', compact('evaluation', 'employees'));
    }

    public function update(Request $request, Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:draft,active,closed',
        ]);

        $evaluation->update([
            'title' => $request->title,
            'description' => $request->description,
            'status' => $request->status,
            'allow_multiple_responses' => $request->has('allow_multiple_responses'),
        ]);

        return redirect()->back()->with('success', 'Evaluación actualizada.');
    }

    public function destroy(Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $evaluation->delete();
        return redirect()->route('evaluations.index')->with('success', 'Evaluación eliminada.');
    }

    public function storeQuestion(Request $request, Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $request->validate([
            'question_text' => 'required|string',
            'type' => 'required|in:scale,text,textarea',
        ]);

        $order = $evaluation->questions()->max('order') + 1;

        $evaluation->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'order' => $order,
            'is_required' => $request->has('is_required'),
        ]);

        return redirect()->back()->with('success', 'Pregunta añadida.');
    }

    public function destroyQuestion(Evaluation $evaluation, EvaluationQuestion $question)
    {
        $this->authorizeAccess($evaluation);
        if ($question->evaluation_id === $evaluation->id) {
            $question->delete();
        }
        return redirect()->back()->with('success', 'Pregunta eliminada.');
    }

    public function storeAssignments(Request $request, Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        $assignedCount = 0;
        foreach ($request->employee_ids as $employeeId) {
            $employee = Employee::find($employeeId);
            if ($employee && $employee->company_id === Auth::user()->company_id) {
                // Check if already assigned
                if (!$evaluation->assignments()->where('employee_id', $employeeId)->exists()) {
                    $evaluation->assignments()->create([
                        'employee_id' => $employeeId,
                    ]);
                    
                    if ($employee->user) {
                        Mail::to($employee->user->email)->queue(new EvaluationAssignedMail($evaluation, $employee->user));
                    }
                    $assignedCount++;
                }
            }
        }

        return redirect()->back()->with('success', "$assignedCount empleados asignados y notificados.");
    }

    public function destroyAssignment(Evaluation $evaluation, EvaluationAssignment $assignment)
    {
        $this->authorizeAccess($evaluation);
        if ($assignment->evaluation_id === $evaluation->id) {
            $assignment->delete();
        }
        return redirect()->back()->with('success', 'Asignación eliminada.');
    }

    public function fill(Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            abort(403, 'Esta evaluación no está activa.');
        }

        $employee = Auth::user()->employee;
        if (!$employee) {
            abort(403, 'No tienes un perfil de empleado asociado.');
        }

        $assignment = $evaluation->assignments()->where('employee_id', $employee->id)->first();
        if (!$assignment) {
            abort(403, 'No estás asignado a esta evaluación.');
        }

        if ($assignment->is_completed && !$evaluation->allow_multiple_responses) {
            return redirect()->route('dashboard')->with('info', 'Ya has completado esta evaluación.');
        }

        $evaluation->load('questions');
        return view('evaluations.fill', compact('evaluation'));
    }

    public function submit(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->status !== 'active') {
            abort(403, 'Esta evaluación no está activa.');
        }

        $employee = Auth::user()->employee;
        if (!$employee) {
            abort(403);
        }

        $assignment = $evaluation->assignments()->where('employee_id', $employee->id)->first();
        if (!$assignment || ($assignment->is_completed && !$evaluation->allow_multiple_responses)) {
            abort(403);
        }

        $rules = [];
        foreach ($evaluation->questions as $question) {
            if ($question->is_required) {
                $rules['q_' . $question->id] = 'required';
            }
            if ($question->type === 'scale') {
                $rules['q_' . $question->id] .= '|nullable|integer|min:1|max:10';
            }
        }

        $request->validate($rules);

        $response = $evaluation->responses()->create([
            'employee_id' => $employee->id,
        ]);

        foreach ($evaluation->questions as $question) {
            $value = $request->input('q_' . $question->id);
            if ($value !== null) {
                $response->answers()->create([
                    'evaluation_question_id' => $question->id,
                    'answer_text' => $question->type !== 'scale' ? $value : null,
                    'answer_scale' => $question->type === 'scale' ? $value : null,
                ]);
            }
        }

        $assignment->update(['is_completed' => true]);

        return redirect()->route('dashboard')->with('success', '¡Gracias! Tus respuestas han sido enviadas correctamente.');
    }

    public function results(Evaluation $evaluation)
    {
        $this->authorizeAccess($evaluation);
        $evaluation->load(['questions', 'responses.employee.user', 'responses.answers']);
        
        return view('evaluations.results', compact('evaluation'));
    }

    private function authorizeAccess(Evaluation $evaluation)
    {
        if ($evaluation->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
