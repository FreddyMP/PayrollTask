<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CalendarEvent;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Listar eventos del usuario autenticado.
     * Soporta ?year, ?month, ?view=mine|team
     */
    public function index(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $view  = $request->input('view', 'mine');
        $user  = $request->user();

        $query = CalendarEvent::with('links', 'user')
            ->where('company_id', $user->company_id)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month);

        if ($view === 'team') {
            $role = $user->role;
            if ($role === 'super') {
                // Super ve todos los eventos de la empresa
            } elseif ($role === 'admin') {
                // Admin ve los propios + supervisor + usuario
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', fn ($u) => $u->whereIn('role', ['supervisor', 'usuario']));
                });
            } elseif ($role === 'supervisor') {
                // Supervisor ve los propios + usuario
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', fn ($u) => $u->where('role', 'usuario'));
                });
            } else {
                // Usuario normal: solo los propios
                $query->where('user_id', $user->id);
            }
        } else {
            $query->where('user_id', $user->id);
        }

        $events = $query->orderBy('event_date')
            ->orderBy('event_time')
            ->get()
            ->map(function ($e) use ($user) {
                return [
                    'id'          => $e->id,
                    'title'       => $e->title,
                    'description' => $e->description,
                    'date'        => $e->event_date->format('Y-m-d'),
                    'day'         => (int) $e->event_date->format('j'),
                    'time'        => Carbon::parse($e->event_time)->format('h:i A'),
                    'time_raw'    => $e->event_time,
                    'user'        => $e->user->name ?? '',
                    'is_owner'    => $e->user_id === $user->id,
                    'links'       => $e->links->map(fn ($l) => [
                        'id'    => $l->id,
                        'url'   => $l->url,
                        'label' => $l->label ?: $l->url,
                    ]),
                ];
            });

        return response()->json([
            'events' => $events,
            'count'  => $events->count(),
            'year'   => (int) $year,
            'month'  => (int) $month,
        ]);
    }

    /**
     * Crear una nueva actividad en el calendario.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'event_date'    => 'required|date',
            'event_time'    => 'required',
            'links'         => 'nullable|array',
            'links.*.url'   => 'required|url|max:2048',
            'links.*.label' => 'nullable|string|max:255',
        ]);

        $user = $request->user();

        $event = CalendarEvent::create([
            'company_id'  => $user->company_id,
            'user_id'     => $user->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date'  => $data['event_date'],
            'event_time'  => $data['event_time'],
        ]);

        if (!empty($data['links'])) {
            foreach ($data['links'] as $link) {
                if (!empty($link['url'])) {
                    $event->links()->create([
                        'url'   => $link['url'],
                        'label' => $link['label'] ?? null,
                    ]);
                }
            }
        }

        $event->load('links', 'user');

        return response()->json([
            'message' => 'Actividad registrada exitosamente.',
            'event'   => [
                'id'          => $event->id,
                'title'       => $event->title,
                'description' => $event->description,
                'date'        => $event->event_date->format('Y-m-d'),
                'time'        => Carbon::parse($event->event_time)->format('h:i A'),
                'time_raw'    => $event->event_time,
                'user'        => $event->user->name ?? '',
                'is_owner'    => true,
                'links'       => $event->links->map(fn ($l) => [
                    'id'    => $l->id,
                    'url'   => $l->url,
                    'label' => $l->label ?: $l->url,
                ]),
            ],
        ], 201);
    }

    /**
     * Editar una actividad existente.
     */
    public function update(Request $request, $id)
    {
        $user  = $request->user();
        $event = CalendarEvent::find($id);

        if (!$event) {
            return response()->json(['message' => 'Actividad no encontrada.'], 404);
        }

        // Verificar que pertenece a la misma empresa
        if ($event->company_id !== $user->company_id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Solo el dueño puede editar su actividad
        if ($event->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso para editar esta actividad.'], 403);
        }

        $data = $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'event_date'    => 'required|date',
            'event_time'    => 'required',
            'links'         => 'nullable|array',
            'links.*.url'   => 'required|url|max:2048',
            'links.*.label' => 'nullable|string|max:255',
        ]);

        $event->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date'  => $data['event_date'],
            'event_time'  => $data['event_time'],
        ]);

        // Sync links: elimina los anteriores e inserta los nuevos
        $event->links()->delete();

        if (!empty($data['links'])) {
            foreach ($data['links'] as $link) {
                if (!empty($link['url'])) {
                    $event->links()->create([
                        'url'   => $link['url'],
                        'label' => $link['label'] ?? null,
                    ]);
                }
            }
        }

        $event->load('links', 'user');

        return response()->json([
            'message' => 'Actividad actualizada exitosamente.',
            'event'   => [
                'id'          => $event->id,
                'title'       => $event->title,
                'description' => $event->description,
                'date'        => $event->event_date->format('Y-m-d'),
                'time'        => Carbon::parse($event->event_time)->format('h:i A'),
                'time_raw'    => $event->event_time,
                'user'        => $event->user->name ?? '',
                'is_owner'    => true,
                'links'       => $event->links->map(fn ($l) => [
                    'id'    => $l->id,
                    'url'   => $l->url,
                    'label' => $l->label ?: $l->url,
                ]),
            ],
        ]);
    }

    /**
     * Eliminar una actividad del calendario.
     */
    public function destroy(Request $request, $id)
    {
        $user  = $request->user();
        $event = CalendarEvent::find($id);

        if (!$event) {
            return response()->json(['message' => 'Actividad no encontrada.'], 404);
        }

        // Verificar que pertenece a la misma empresa
        if ($event->company_id !== $user->company_id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Solo el dueño puede eliminar su actividad
        if ($event->user_id !== $user->id) {
            return response()->json(['message' => 'No tienes permiso para eliminar esta actividad.'], 403);
        }

        $event->links()->delete();
        $event->delete();

        return response()->json([
            'message' => 'Actividad eliminada exitosamente.',
        ]);
    }
}
