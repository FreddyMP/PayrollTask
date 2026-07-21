<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRequest;
use App\Models\RequestAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SolicitudesController extends Controller
{
    /**
     * Display a listing of the requests.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $companyId = $user->company_id;

        $query = UserRequest::where('company_id', $companyId)->with(['user', 'reviewer', 'approvedBy', 'attachments.user']);

        if (!$user->isSupervisor()) {
            $query->where('user_id', $user->id);
        }

        // Filtro por tipo
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    /**
     * Store a newly created request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type'                => 'required|in:vacation,permission,work_letter,overtime',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'description'         => 'nullable|string',
            'attachments.*'       => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:30720',
            'url'                 => 'nullable|url',
            // Overtime fields
            'overtime_date'       => 'required_if:type,overtime|nullable|date',
            'overtime_start'      => 'required_if:type,overtime|nullable|date_format:H:i',
            'overtime_end'        => 'required_if:type,overtime|nullable|date_format:H:i|after:overtime_start',
            'approved_by_user_id' => 'required|exists:users,id',
        ]);

        $user = auth()->user();

        $data = $request->only([
            'type', 'start_date', 'end_date', 'description',
            'overtime_date', 'overtime_start', 'overtime_end', 'approved_by_user_id'
        ]);

        $data['user_id']   = $user->id;
        $data['company_id'] = $user->company_id;
        $data['status']    = 'pending';

        // Calcular horas extra en el servidor
        if ($request->type === 'overtime' && $request->overtime_start && $request->overtime_end) {
            $start = \Carbon\Carbon::parse($request->overtime_start);
            $end   = \Carbon\Carbon::parse($request->overtime_end);
            $data['overtime_hours'] = round($start->diffInMinutes($end) / 60, 2);
        }

        $userRequest = UserRequest::create($data);

        // Subir archivos físicos si existen
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('requests/attachments', config('filesystems.default'));
                $mimeType = $file->getMimeType();
                $fileType = str_contains($mimeType, 'video') ? 'video' : 'image';

                RequestAttachment::create([
                    'user_request_id' => $userRequest->id,
                    'user_id' => $user->id,
                    'file_path' => $path,
                    'file_type' => $fileType,
                ]);
            }
        }

        // Descargar archivo desde URL si existe
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

                    $disk = config('filesystems.default');
                    $s3Path = 'requests/attachments/' . Str::random(40) . '_' . $filename;
                    Storage::disk($disk)->put($s3Path, $fileContent);

                    $contentType = $response->header('Content-Type') ?? '';
                    $fileType = str_contains($contentType, 'video') ? 'video' : 'image';

                    RequestAttachment::create([
                        'user_request_id' => $userRequest->id,
                        'user_id' => $user->id,
                        'file_path' => $s3Path,
                        'file_type' => $fileType,
                    ]);
                } else {
                    logger()->error('Failed to download request attachment from URL: ' . $url . ' status: ' . $response->status());
                }
            } catch (\Exception $e) {
                logger()->error('Error processing request URL attachment: ' . $e->getMessage());
            }
        }

        // Enviar correo a los supervisores/admins de la empresa
        $supervisors = User::where('company_id', $userRequest->company_id)
            ->whereIn('role', ['super', 'admin', 'supervisor'])
            ->get();

        foreach ($supervisors as $supervisor) {
            try {
                Mail::to($supervisor->email)->send(new \App\Mail\UserRequestCreated($userRequest->load('attachments')));
            } catch (\Exception $e) {
                logger()->error('Error sending request creation email from API: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitud creada exitosamente.',
            'data' => $userRequest->load('attachments')
        ], 201);
    }
}
