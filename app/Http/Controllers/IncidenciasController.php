<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\IncidentReported;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
            'url' => 'nullable|url',
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

        if ($request->filled('url')) {
            $url = $request->input('url');
            try {
                $response = Http::get($url);
                if ($response->successful()) {
                    $fileContent = $response->body();
                    $pathInfo = pathinfo(parse_url($url, PHP_URL_PATH));
                    $filename = !empty($pathInfo['basename']) ? $pathInfo['basename'] : Str::random(10);
                    
                    if (empty($pathInfo['extension'])) {
                        $contentType = $response->header('Content-Type');
                        $extension = '';
                        if (str_contains($contentType, 'image/jpeg') || str_contains($contentType, 'image/jpg')) {
                            $extension = '.jpg';
                        } elseif (str_contains($contentType, 'image/png')) {
                            $extension = '.png';
                        } elseif (str_contains($contentType, 'image/gif')) {
                            $extension = '.gif';
                        } elseif (str_contains($contentType, 'video/mp4')) {
                            $extension = '.mp4';
                        } elseif (str_contains($contentType, 'video/quicktime')) {
                            $extension = '.mov';
                        }
                        $filename .= $extension;
                    }

                    $s3Path = 'incidents/attachments/' . Str::random(40) . '_' . $filename;
                    Storage::disk('s3')->put($s3Path, $fileContent);

                    $contentType = $response->header('Content-Type') ?? '';
                    $fileType = str_contains($contentType, 'video') ? 'video' : 'image';

                    IncidentAttachment::create([
                        'incident_id' => $incident->id,
                        'user_id' => $user->id,
                        'file_path' => $s3Path,
                        'file_type' => $fileType,
                    ]);
                } else {
                    logger()->error('Failed to download incident attachment from URL: ' . $url . ' status: ' . $response->status());
                }
            } catch (\Exception $e) {
                logger()->error('Error processing incident URL attachment: ' . $e->getMessage());
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
