<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncidentReported;
use Illuminate\Support\Facades\Storage;

class IncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = Incident::with(['user', 'attachments'])->where('company_id', $user->company_id);

        // If not admin, super, or supervisor, only see own incidents
        if (!$user->isSupervisor()) {
            $query->where('user_id', $user->id);
        }

        $incidents = $query->latest()->get();

        return view('incidents.index', compact('incidents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:25600', // max 25MB
        ]);

        $incident = Incident::create([
            'company_id' => auth()->user()->company_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'pending',
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                // Store directly in S3
                $path = $file->store('incidents/attachments', 's3');
                
                // Determine file type
                $mimeType = $file->getMimeType();
                $fileType = str_contains($mimeType, 'video') ? 'video' : 'image';

                IncidentAttachment::create([
                    'incident_id' => $incident->id,
                    'user_id' => auth()->id(),
                    'file_path' => $path,
                    'file_type' => $fileType,
                ]);
            }
        }

        // Envío de correo si está configurado en el .env
        $targetEmail = env('INCIDENTS_EMAIL_ADDRESS');
        if ($targetEmail) {
            try {
                // Eager load relationships to include attachments in mail template
                $incident->load(['user', 'attachments']);
                Mail::to($targetEmail)->send(new IncidentReported($incident));
            } catch (\Exception $e) {
                // Registrar el error para que la creación de la incidencia no falle por problemas de envío de correo
                logger()->error('Error enviando correo de incidencia: ' . $e->getMessage());
            }
        }

        return redirect()->route('incidencias.index')->with('success', 'Incidencia reportada con éxito.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Incident $incidencia)
    {
        // Only admins/supervisors should be able to update status
        if (!auth()->user()->isSupervisor()) {
            abort(403, 'No tienes permiso para actualizar esta incidencia.');
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
        ]);

        $incidencia->update([
            'status' => $request->status,
        ]);

        return redirect()->route('incidencias.index')->with('success', 'Estado de la incidencia actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incidencia)
    {
        // Allow user to delete their own if pending, or admin to delete
        $user = auth()->user();
        
        if (!$user->isAdmin() && $incidencia->user_id !== $user->id) {
            abort(403, 'No tienes permiso para eliminar esta incidencia.');
        }

        // Delete associated S3 attachments
        foreach ($incidencia->attachments as $attachment) {
            try {
                Storage::disk('s3')->delete($attachment->file_path);
            } catch (\Exception $e) {
                logger()->error('Error eliminando archivo adjunto de S3: ' . $e->getMessage());
            }
        }

        $incidencia->delete();

        return redirect()->route('incidencias.index')->with('success', 'Incidencia eliminada con éxito.');
    }
}
