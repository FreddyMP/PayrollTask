<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncidentReported;

class IncidenciasController extends Controller
{
    /**
     * Display a listing of the resource (filtered by company).
     */
    public function index()
    {
        $user = auth()->user();
        
        $incidents = Incident::with(['user', 'attachments'])
            ->where('company_id', $user->company_id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $incidents
        ]);
    }

    /**
     * Store a newly created incident.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:25600', // max 25MB
        ]);

        $user = auth()->user();

        $incident = Incident::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('incidents/attachments', 's3');
                $mimeType = $file->getMimeType();
                $fileType = str_contains($mimeType, 'video') ? 'video' : 'image';

                IncidentAttachment::create([
                    'incident_id' => $incident->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $fileType,
                ]);
            }
        }

        // Send email notification
        $targetEmail = env('INCIDENTS_EMAIL_ADDRESS');
        if ($targetEmail) {
            try {
                $incident->load(['user', 'attachments']);
                Mail::to($targetEmail)->send(new IncidentReported($incident));
            } catch (\Exception $e) {
                logger()->error('Error sending incident email from API: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Incidencia reportada con éxito.',
            'data' => $incident->load('attachments')
        ], 201);
    }

    /**
     * Update the specified incident in storage.
     * Note: status, user_id, company_id, and id are not editable.
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:incidents,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'priority' => 'sometimes|required|in:low,medium,high',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:25600', // max 25MB
        ]);

        $user = auth()->user();
        
        // Ensure we only retrieve the incident belonging to the authenticated user's company
        $incident = Incident::where('company_id', $user->company_id)->findOrFail($request->id);

        // Filter the request: only allow updating title, description, and priority
        // Reject changes to status, user_id, company_id, and id
        $updateData = $request->only(['title', 'description', 'priority']);

        $incident->update($updateData);

        // Upload and associate new attachments if provided
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('incidents/attachments', 's3');
                $mimeType = $file->getMimeType();
                $fileType = str_contains($mimeType, 'video') ? 'video' : 'image';

                IncidentAttachment::create([
                    'incident_id' => $incident->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $fileType,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Incidencia actualizada con éxito.',
            'data' => $incident->load('attachments')
        ]);
    }
}
