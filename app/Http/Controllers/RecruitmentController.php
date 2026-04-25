<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vacancy;
use App\Models\RecruitmentStep;
use App\Models\Candidate;
use App\Models\CandidateProgress;
use App\Models\User;
use App\Models\ApplicationForm;
use App\Models\ApplicationFormField;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class RecruitmentController extends Controller
{
    public function index()
    {
        $vacancies = Vacancy::where('company_id', auth()->user()->company_id)
            ->withCount('candidates')
            ->latest()
            ->paginate(10);
        
        $applicationForm = ApplicationForm::firstOrCreate([
            'company_id' => auth()->user()->company_id
        ]);
        $applicationForm->load('fields');

        return view('recruitment.index', compact('vacancies', 'applicationForm'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        Vacancy::create([
            'company_id' => auth()->user()->company_id,
            'title' => $request->title,
            'department' => $request->department,
            'description' => $request->description,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Vacante creada correctamente.');
    }

    public function show(Vacancy $vacancy)
    {
        $this->authorizeAccess($vacancy);
        $vacancy->load(['steps.responsible', 'candidates.progress.step']);
        $users = User::where('company_id', $vacancy->company_id)->get();
        return view('recruitment.show', compact('vacancy', 'users'));
    }

    public function addStep(Request $request, Vacancy $vacancy)
    {
        $this->authorizeAccess($vacancy);
        $request->validate([
            'name' => 'required|string|max:255',
            'responsible_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:0|max:100',
        ]);

        $vacancy->steps()->create([
            'name' => $request->name,
            'responsible_id' => $request->responsible_id,
            'points' => $request->points,
            'sort_order' => $vacancy->steps()->count() + 1,
        ]);

        return redirect()->back()->with('success', 'Paso de reclutamiento agregado.');
    }

    public function addCandidate(Request $request, Vacancy $vacancy)
    {
        $this->authorizeAccess($vacancy);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cvs', 'public');
        }

        $candidate = $vacancy->candidates()->create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'cv_path' => $cvPath,
            'status' => 'active',
        ]);

        // Initialize progress for the first step if exists
        $firstStep = $vacancy->steps()->first();
        if ($firstStep) {
            $candidate->progress()->create([
                'recruitment_step_id' => $firstStep->id,
                'status' => 'pending',
                'score' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Candidato registrado correctamente.');
    }

    public function updateProgress(Request $request, Candidate $candidate)
    {
        if ($candidate->vacancy->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $request->validate([
            'recruitment_step_id' => 'required|exists:recruitment_steps,id',
            'status' => 'required|in:completed,discarded',
            'score' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $progress = CandidateProgress::updateOrCreate(
            ['candidate_id' => $candidate->id, 'recruitment_step_id' => $request->recruitment_step_id],
            [
                'status' => $request->status,
                'score' => $request->score ?? 0,
                'notes' => $request->notes,
                'completed_at' => now(),
            ]
        );

        if ($request->status === 'discarded') {
            $candidate->update(['status' => 'discarded']);
        } else {
            // Check if there is a next step
            $currentStep = RecruitmentStep::find($request->recruitment_step_id);
            $nextStep = RecruitmentStep::where('vacancy_id', $candidate->vacancy_id)
                ->where('sort_order', '>', $currentStep->sort_order)
                ->orderBy('sort_order')
                ->first();

            if ($nextStep) {
                CandidateProgress::firstOrCreate([
                    'candidate_id' => $candidate->id,
                    'recruitment_step_id' => $nextStep->id,
                ], [
                    'status' => 'pending',
                    'score' => 0,
                ]);
            } else {
                // Last step completed!
                // Maybe set status to "completed" or similar if we had that status
            }
        }

        return redirect()->back()->with('success', 'Progreso actualizado.');
    }

    public function ranking(Vacancy $vacancy)
    {
        $this->authorizeAccess($vacancy);
        // Get candidates who are NOT discarded and have completed all steps?
        // Or just all candidates sorted by total points.
        $candidates = $candidateResults = $vacancy->candidates()
            ->where('status', '!=', 'discarded')
            ->get()
            ->sortByDesc(function ($candidate) {
                return $candidate->total_points;
            });

        return view('recruitment.ranking', compact('vacancy', 'candidates'));
    }

    public function storeField(Request $request, ApplicationForm $applicationForm)
    {
        if ($applicationForm->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,long_text,textarea,date,integer,decimal,table',
            'columns' => 'nullable|string', // Comma separated columns for table
        ]);

        $options = null;
        if ($request->type === 'table' && $request->columns) {
            $options = ['columns' => array_map('trim', explode(',', $request->columns))];
        }

        $applicationForm->fields()->create([
            'label' => $request->label,
            'type' => $request->type,
            'options' => $options,
            'sort_order' => $applicationForm->fields()->count() + 1,
        ]);

        return redirect()->back()->with('success', 'Campo agregado a la hoja de solicitud.');
    }

    public function deleteField(ApplicationFormField $field)
    {
        $field->delete();
        return redirect()->back()->with('success', 'Campo eliminado.');
    }

    public function printForm(ApplicationForm $applicationForm)
    {
        if ($applicationForm->company_id !== auth()->user()->company_id) {
            abort(403);
        }
        $applicationForm->load(['fields', 'company']);
        $pdf = Pdf::loadView('recruitment.pdf.application_form', compact('applicationForm'));
        return $pdf->download('hoja_solicitud_' . $applicationForm->company->name . '.pdf');
    }

    private function authorizeAccess(Vacancy $vacancy)
    {
        if ($vacancy->company_id !== auth()->user()->company_id) {
            abort(403);
        }
    }
}
