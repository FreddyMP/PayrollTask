<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\CalendarEventLink;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    /**
     * API endpoint: return events for a given month/year as JSON.
     * Supports ?view=mine (default) or ?view=team (role-based hierarchy).
     */
    public function apiEvents(Request $request)
    {
        $year  = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $view  = $request->input('view', 'mine');
        $user  = auth()->user();

        $query = CalendarEvent::with('links', 'user')
            ->where('company_id', $user->company_id)
            ->whereYear('event_date', $year)
            ->whereMonth('event_date', $month);

        if ($view === 'team') {
            // Role-based visibility hierarchy
            $role = $user->role;
            if ($role === 'super') {
                // Super sees all events in the company — no extra filter
            } elseif ($role === 'admin') {
                // Admin sees own + supervisor + usuario
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', fn ($u) => $u->whereIn('role', ['supervisor', 'usuario']));
                });
            } elseif ($role === 'supervisor') {
                // Supervisor sees own + usuario
                $query->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->orWhereHas('user', fn ($u) => $u->where('role', 'usuario'));
                });
            } else {
                // Usuario sees only own
                $query->where('user_id', $user->id);
            }
        } else {
            // Default: only own events
            $query->where('user_id', $user->id);
        }

        $events = $query->orderBy('event_date')
            ->orderBy('event_time')
            ->get()
            ->map(function ($e) {
                return [
                    'id'          => $e->id,
                    'title'       => $e->title,
                    'description' => $e->description,
                    'date'        => $e->event_date->format('Y-m-d'),
                    'day'         => (int) $e->event_date->format('j'),
                    'time'        => \Carbon\Carbon::parse($e->event_time)->format('h:i A'),
                    'time_raw'    => $e->event_time,
                    'user'        => $e->user->name ?? '',
                    'is_owner'    => $e->user_id === auth()->id(),
                    'links'       => $e->links->map(fn ($l) => [
                        'id'    => $l->id,
                        'url'   => $l->url,
                        'label' => $l->label ?: $l->url,
                    ]),
                ];
            });

        return response()->json($events);
    }

    public function create(Request $request)
    {
        $date = $request->query('date', now()->format('Y-m-d'));
        return view('calendar.create', compact('date'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'event_date'   => 'required|date',
            'event_time'   => 'required',
            'links'        => 'nullable|array',
            'links.*.url'  => 'required|url|max:2048',
            'links.*.label'=> 'nullable|string|max:255',
        ]);

        $event = CalendarEvent::create([
            'company_id'  => auth()->user()->company_id,
            'user_id'     => auth()->id(),
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

        return redirect()->route('calendar.index')->with('success', 'Actividad registrada exitosamente.');
    }

    public function edit(CalendarEvent $calendar)
    {
        if ($calendar->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $calendar->load('links');
        return view('calendar.edit', ['event' => $calendar]);
    }

    public function update(Request $request, CalendarEvent $calendar)
    {
        if ($calendar->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'event_date'   => 'required|date',
            'event_time'   => 'required',
            'links'        => 'nullable|array',
            'links.*.url'  => 'required|url|max:2048',
            'links.*.label'=> 'nullable|string|max:255',
        ]);

        $calendar->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date'  => $data['event_date'],
            'event_time'  => $data['event_time'],
        ]);

        // Sync links: delete old, insert new
        $calendar->links()->delete();

        if (!empty($data['links'])) {
            foreach ($data['links'] as $link) {
                if (!empty($link['url'])) {
                    $calendar->links()->create([
                        'url'   => $link['url'],
                        'label' => $link['label'] ?? null,
                    ]);
                }
            }
        }

        return redirect()->route('calendar.index')->with('success', 'Actividad actualizada exitosamente.');
    }

    public function destroy(CalendarEvent $calendar)
    {
        if ($calendar->company_id !== auth()->user()->company_id) {
            abort(403);
        }

        $calendar->delete();

        return redirect()->route('calendar.index')->with('success', 'Actividad eliminada exitosamente.');
    }
}
