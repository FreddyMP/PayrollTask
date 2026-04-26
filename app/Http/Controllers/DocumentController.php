<?php

namespace App\Http\Controllers;

use App\Models\DocumentTemplate;
use App\Models\Employee;
use App\Models\CompanyField;
use App\Services\DocumentProcessorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    protected $processor;

    public function __construct(DocumentProcessorService $processor)
    {
        $this->processor = $processor;
    }

    public function index()
    {
        $templates = DocumentTemplate::where('company_id', Auth::user()->company_id)->get();
        return view('documents.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string',
            'file' => 'nullable|file|mimes:docx,txt,html|max:10240',
            'content' => 'nullable|string',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('templates', 'public');
        }

        DocumentTemplate::create([
            'company_id' => Auth::user()->company_id,
            'title' => $request->title,
            'file_path' => $filePath,
            'content' => $request->content,
            'category' => $request->category ?? 'general',
        ]);

        return redirect()->back()->with('success', 'Plantilla guardada correctamente.');
    }

    public function show(DocumentTemplate $template)
    {
        $this->authorizeAccess($template);
        $employees = Employee::where('company_id', Auth::user()->company_id)->with('user')->get();
        $fields = $template->variables;
        return view('documents.show', compact('template', 'employees', 'fields'));
    }

    public function generate(Request $request, DocumentTemplate $template)
    {
        $this->authorizeAccess($template);
        
        $employee = null;
        if ($request->employee_id) {
            $employee = Employee::where('id', $request->employee_id)
                ->where('company_id', Auth::user()->company_id)
                ->first();
        }

        if ($template->file_path && str_ends_with($template->file_path, '.docx')) {
            $tempFile = $this->processor->processDocx($template->file_path, $template, $employee);
            return response()->download($tempFile, $template->title . '.docx')->deleteFileAfterSend(true);
        }

        $processedContent = $this->processor->process($template->content ?? '', $template, $employee);

        if ($request->format === 'pdf') {
            $pdf = Pdf::loadHTML($processedContent);
            return $pdf->download($template->title . '.pdf');
        }

        return view('documents.preview', [
            'content' => $processedContent,
            'template' => $template
        ]);
    }

    public function destroy(DocumentTemplate $template)
    {
        $this->authorizeAccess($template);
        $template->delete();
        return redirect()->route('documents.index')->with('success', 'Plantilla eliminada.');
    }

    private function authorizeAccess(DocumentTemplate $template)
    {
        if ($template->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
