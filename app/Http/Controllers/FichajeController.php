<?php

namespace App\Http\Controllers;

use App\Models\Fichaje;
use App\Models\Employee;
use Illuminate\Http\Request;

class FichajeController extends Controller
{
    /**
     * Endpoint API para recibir el inicio o cierre de día desde un tercero.
     */
    public function storeApi(Request $request)
    {
        $request->validate([
            'id_number' => 'required|string',
            'action' => 'required|in:clock_in,clock_out',
            'timestamp' => 'required|date',
        ]);

        $employee = Employee::where('id_number', $request->id_number)->first();

        if (!$employee) {
            return response()->json(['error' => 'Empleado no encontrado'], 404);
        }

        if ($request->action === 'clock_in') {
            // Check if already clocked in today
            $fichaje = Fichaje::where('employee_id', $employee->id)
                ->whereDate('clock_in', \Carbon\Carbon::parse($request->timestamp)->toDateString())
                ->first();
                
            if ($fichaje) {
                return response()->json(['error' => 'Ya existe un registro de entrada para hoy'], 400);
            }

            // Guardamos las horas de descanso del empleado en este registro para tener histórico inmutable
            $fichaje = Fichaje::create([
                'employee_id' => $employee->id,
                'clock_in' => $request->timestamp,
                'break_start' => $employee->break_start,
                'break_end' => $employee->break_end,
            ]);

            return response()->json(['message' => 'Entrada registrada exitosamente', 'data' => $fichaje], 201);
        }

        if ($request->action === 'clock_out') {
            $fichaje = Fichaje::where('employee_id', $employee->id)
                ->whereDate('clock_in', \Carbon\Carbon::parse($request->timestamp)->toDateString())
                ->first();

            if (!$fichaje) {
                return response()->json(['error' => 'No existe un registro de entrada para hoy'], 400);
            }

            if ($fichaje->clock_out) {
                return response()->json(['error' => 'Ya existe un registro de salida para hoy'], 400);
            }

            // Calcular el total de horas
            $start = \Carbon\Carbon::parse($fichaje->clock_in);
            $end = \Carbon\Carbon::parse($request->timestamp);
            $totalMinutes = $start->diffInMinutes($end);

            if ($fichaje->break_start && $fichaje->break_end) {
                $breakStart = \Carbon\Carbon::parse($fichaje->clock_in->toDateString() . ' ' . $fichaje->break_start);
                $breakEnd = \Carbon\Carbon::parse($fichaje->clock_in->toDateString() . ' ' . $fichaje->break_end);
                
                // Asegurarse de que el descanso ocurre dentro del turno
                if ($breakStart->between($start, $end) && $breakEnd->between($start, $end)) {
                    $breakMinutes = $breakStart->diffInMinutes($breakEnd);
                    $totalMinutes -= $breakMinutes;
                }
            }

            $totalHours = round($totalMinutes / 60, 2);

            $fichaje->update([
                'clock_out' => $request->timestamp,
                'total_hours' => $totalHours,
            ]);

            return response()->json(['message' => 'Salida registrada exitosamente', 'data' => $fichaje], 200);
        }
    }

    /**
     * Display a listing of the resource for the web panel.
     */
    public function index(Request $request)
    {
        // En un entorno multi-tenant real, deberíamos filtrar por company_id
        $companyId = auth()->user()->company_id;
        
        $query = Fichaje::with('employee.user')
            ->whereHas('employee', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            });
            
        if ($request->filled('date')) {
            $query->whereDate('clock_in', $request->date);
        } else {
            $query->whereDate('clock_in', now()->toDateString());
        }

        $fichajes = $query->latest('clock_in')->get();

        return view('fichajes.index', compact('fichajes'));
    }
}
