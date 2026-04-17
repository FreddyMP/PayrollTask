<?php

namespace App\Http\Controllers;

use App\Mail\UserRequestCreated;
use App\Mail\UserRequestReviewed;
use App\Models\User;
use App\Models\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $query = UserRequest::where('company_id', $companyId)->with(['user', 'reviewer', 'approvedBy', 'attachments.user']);

        if (!$user->isSupervisor()) {
            $query->where('user_id', $user->id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(15);

        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $user = Auth::user();
        $levels = ['super' => 1, 'admin' => 2, 'supervisor' => 3, 'usuario' => 4];
        $userLevel = $levels[$user->role] ?? 99;

        // Usuarios con nivel numérico MENOR (rol superior) al del usuario actual
        $approvers = User::where('company_id', $user->company_id)
            ->where('id', '!=', $user->id)
            ->whereIn('role', array_keys(array_filter($levels, fn($level) => $level < $userLevel)))
            ->orderBy('name')
            ->get();

        return view('requests.create', compact('approvers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'                => 'required|in:vacation,permission,work_letter,overtime',
            'start_date'          => 'nullable|date',
            'end_date'            => 'nullable|date|after_or_equal:start_date',
            'description'         => 'nullable|string',
            'attachments.*'       => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi,wmv|max:30720',
            // Campos de horas extra (requeridos solo si type=overtime)
            'overtime_date'       => 'required_if:type,overtime|nullable|date',
            'overtime_start'      => 'required_if:type,overtime|nullable|date_format:H:i',
            'overtime_end'        => 'required_if:type,overtime|nullable|date_format:H:i|after:overtime_start',
            'approved_by_user_id' => 'required_if:type,overtime|nullable|exists:users,id',
        ]);

        $data['user_id']   = Auth::id();
        $data['company_id'] = Auth::user()->company_id;
        $data['status']    = 'pending';

        // Calcular horas extra en el servidor
        if ($request->type === 'overtime' && $request->overtime_start && $request->overtime_end) {
            $start = \Carbon\Carbon::parse($request->overtime_start);
            $end   = \Carbon\Carbon::parse($request->overtime_end);
            $data['overtime_hours'] = round($start->diffInMinutes($end) / 60, 2);
        }

        $userRequest = UserRequest::create($data);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('requests/attachments', 'public');
                \App\Models\RequestAttachment::create([
                    'user_request_id' => $userRequest->id,
                    'user_id' => Auth::id(),
                    'file_path' => $path,
                    'file_type' => $this->getFileType($file),
                ]);
            }
        }

        // Enviar correo a los supervisores/admins de la empresa
        $supervisors = User::where('company_id', $userRequest->company_id)
            ->whereIn('role', ['super', 'admin', 'supervisor'])
            ->get();

        foreach ($supervisors as $supervisor) {
            Mail::to($supervisor->email)->send(new UserRequestCreated($userRequest));
        }

        return redirect()->route('requests.index')->with('success', 'Solicitud creada exitosamente y notificada por correo.');
    }

    public function review(Request $request, UserRequest $userRequest)
    {
        if ($userRequest->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $userRequest->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notificar al usuario solicitante
        Mail::to($userRequest->user->email)->send(new UserRequestReviewed($userRequest));

        return redirect()->route('requests.index')->with('success', 'Solicitud actualizada.');
    }

    public function destroy(UserRequest $userRequest)
    {
        if ($userRequest->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $userRequest->delete();
        return redirect()->route('requests.index')->with('success', 'Solicitud eliminada.');
    }

    private function getFileType($file)
    {
        $mime = $file->getMimeType();
        return str_contains($mime, 'video') ? 'video' : 'image';
    }
}
