<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationAssignment;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    /**
     * Listar evaluaciones disponibles.
     * Si se proporciona o detecta un empleado (mediante token Sanctum o parámetro employee_id / id_number / email),
     * lista las evaluaciones asignadas a dicho empleado con su estado (pendiente / completada).
     * Si es llamado a nivel de empresa/admin, lista las evaluaciones de la empresa.
     */
    public function index(Request $request)
    {
        $user = auth('sanctum')->user() ?? $request->user();
        $employee = $this->resolveEmployee($request);

        // 1. Contexto de Empleado (Token o parámetros de identificación)
        if ($employee) {
            $query = EvaluationAssignment::where('employee_id', $employee->id)
                ->with(['evaluation.questions']);

            // Filtro por estado de la asignación (pending / completed)
            if ($request->filled('status')) {
                if ($request->status === 'pending') {
                    $query->where(function ($q) {
                        $q->where('is_completed', false)
                          ->orWhereHas('evaluation', function ($sub) {
                              $sub->where('allow_multiple_responses', true);
                          });
                    });
                } elseif ($request->status === 'completed') {
                    $query->where('is_completed', true);
                }
            }

            // Solo evaluaciones activas por defecto, a menos que se solicite otro estado
            $query->whereHas('evaluation', function ($q) use ($request) {
                if ($request->filled('eval_status')) {
                    $q->where('status', $request->eval_status);
                } else {
                    $q->where('status', 'active');
                }
            });

            $assignments = $query->latest()->get();

            $evaluations = $assignments->map(function ($assignment) {
                $eval = $assignment->evaluation;
                return [
                    'id'                       => $eval->id,
                    'title'                    => $eval->title,
                    'description'              => $eval->description,
                    'status'                   => $eval->status,
                    'allow_multiple_responses' => (bool) $eval->allow_multiple_responses,
                    'assignment_id'            => $assignment->id,
                    'is_completed'             => (bool) $assignment->is_completed,
                    'can_submit'               => !$assignment->is_completed || (bool) $eval->allow_multiple_responses,
                    'questions_count'          => $eval->questions->count(),
                    'created_at'               => $eval->created_at?->toIso8601String(),
                ];
            });

            return response()->json([
                'success'  => true,
                'employee' => [
                    'id'        => $employee->id,
                    'id_number' => $employee->id_number,
                    'name'      => $employee->user ? $employee->user->name : null,
                    'email'     => $employee->user ? $employee->user->email : null,
                ],
                'count'    => $evaluations->count(),
                'data'     => $evaluations,
            ]);
        }

        // 2. Contexto de Administrador / Empresa (sin empleado específico)
        $companyId = $user ? $user->company_id : $request->input('company_id');
        if ($companyId) {
            $query = Evaluation::where('company_id', $companyId);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $evaluations = $query->withCount(['questions', 'assignments', 'responses'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($eval) {
                    return [
                        'id'                       => $eval->id,
                        'title'                    => $eval->title,
                        'description'              => $eval->description,
                        'status'                   => $eval->status,
                        'allow_multiple_responses' => (bool) $eval->allow_multiple_responses,
                        'questions_count'          => $eval->questions_count,
                        'assignments_count'        => $eval->assignments_count,
                        'responses_count'          => $eval->responses_count,
                        'created_at'               => $eval->created_at?->toIso8601String(),
                    ];
                });

            return response()->json([
                'success' => true,
                'count'   => $evaluations->count(),
                'data'    => $evaluations,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Debe autenticarse como empleado o proporcionar un identificador de empleado (employee_id, id_number o email), o especificar company_id.',
        ], 400);
    }

    /**
     * Obtener el detalle y las preguntas de una evaluación para responderla.
     * Aplica la misma lógica de validación que el método `fill` de la web.
     */
    public function show(Request $request, $id)
    {
        $evaluation = Evaluation::with(['questions' => fn($q) => $q->orderBy('order')])->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluación no encontrada.',
            ], 404);
        }

        // Validación web: Debe estar activa
        if ($evaluation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Esta evaluación no está activa.',
                'status'  => $evaluation->status,
            ], 403);
        }

        $employee = $this->resolveEmployee($request, $evaluation);
        $assignment = null;
        $canSubmit = false;

        if ($employee) {
            $assignment = $evaluation->assignments()->where('employee_id', $employee->id)->first();
            if ($assignment) {
                $canSubmit = !$assignment->is_completed || (bool) $evaluation->allow_multiple_responses;
            }
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                       => $evaluation->id,
                'company_id'               => $evaluation->company_id,
                'title'                    => $evaluation->title,
                'description'              => $evaluation->description,
                'status'                   => $evaluation->status,
                'allow_multiple_responses' => (bool) $evaluation->allow_multiple_responses,
                'is_assigned'              => $assignment !== null,
                'is_completed'             => $assignment ? (bool) $assignment->is_completed : false,
                'can_submit'               => $canSubmit,
                'questions'                => $evaluation->questions->map(function ($q) {
                    $item = [
                        'id'            => $q->id,
                        'order'         => $q->order,
                        'question_text' => $q->question_text,
                        'type'          => $q->type, // scale, boolean, text, textarea
                        'is_required'   => (bool) $q->is_required,
                    ];

                    if ($q->type === 'scale') {
                        $item['scale_range'] = [
                            'min' => 1,
                            'max' => 10,
                            'step' => 1,
                        ];
                    } elseif ($q->type === 'boolean') {
                        $item['options'] = [
                            ['value' => 1, 'label' => 'Sí'],
                            ['value' => 0, 'label' => 'No'],
                        ];
                    }

                    return $item;
                }),
            ],
            'employee' => $employee ? [
                'id'        => $employee->id,
                'id_number' => $employee->id_number,
                'name'      => $employee->user ? $employee->user->name : null,
            ] : null,
        ]);
    }

    /**
     * Responder / Enviar respuestas de una evaluación.
     * Implementa con exactitud la misma lógica de negocio y validación que la web (`EvaluationController::submit`).
     */
    public function submit(Request $request, $id)
    {
        $evaluation = Evaluation::with('questions')->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluación no encontrada.',
            ], 404);
        }

        // 1. Misma lógica web: Comprobar que la evaluación esté activa
        if ($evaluation->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Esta evaluación no está activa.',
            ], 403);
        }

        // 2. Misma lógica web: Resolver y verificar empleado
        $employee = $this->resolveEmployee($request, $evaluation);
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró el perfil de empleado asociado. Debe autenticarse como empleado o proporcionar employee_id, id_number o email.',
            ], 403);
        }

        // 3. Misma lógica web: Comprobar que el empleado esté asignado
        $assignment = $evaluation->assignments()->where('employee_id', $employee->id)->first();
        if (!$assignment) {
            return response()->json([
                'success' => false,
                'message' => 'El empleado no está asignado a esta evaluación.',
            ], 403);
        }

        // 4. Misma lógica web: Comprobar si ya fue completada y si permite múltiples respuestas
        if ($assignment->is_completed && !$evaluation->allow_multiple_responses) {
            return response()->json([
                'success' => false,
                'message' => 'Ya has completado esta evaluación.',
            ], 403);
        }

        // 5. Extraer y normalizar respuestas enviadas
        // Soporta formato web (q_1, q_2...), lista JSON (answers: [{question_id: 1, value: ...}]) o mapa JSON (answers: {1: ...})
        $submittedAnswers = $this->extractAnswers($request);

        // 6. Misma lógica web: Construcción y ejecución de las reglas de validación
        $rules = [];
        $validationData = [];
        $customAttributes = [];

        foreach ($evaluation->questions as $question) {
            $key = 'q_' . $question->id;
            $rawVal = $submittedAnswers[$question->id] ?? $request->input($key);

            // Normalización para booleanos (acepta true/false, 1/0, "1"/"0")
            if ($question->type === 'boolean' && $rawVal !== null) {
                if ($rawVal === true || $rawVal === 'true' || $rawVal === 1 || $rawVal === '1') {
                    $rawVal = 1;
                } elseif ($rawVal === false || $rawVal === 'false' || $rawVal === 0 || $rawVal === '0') {
                    $rawVal = 0;
                }
            }

            $validationData[$key] = $rawVal;

            $questionRules = [];
            if ($question->is_required) {
                $questionRules[] = 'required';
            } else {
                $questionRules[] = 'nullable';
            }

            if ($question->type === 'scale') {
                $questionRules[] = 'integer';
                $questionRules[] = 'min:1';
                $questionRules[] = 'max:10';
            } elseif ($question->type === 'boolean') {
                $questionRules[] = 'in:0,1';
            } elseif (in_array($question->type, ['text', 'textarea'])) {
                $questionRules[] = 'string';
            }

            $rules[$key] = $questionRules;
            $customAttributes[$key] = "pregunta '{$question->question_text}' (ID: {$question->id})";
        }

        $validator = Validator::make($validationData, $rules, [], $customAttributes);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación en las respuestas.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // 7. Misma lógica web: Persistir respuesta, respuestas por pregunta y actualizar estado de asignación
        $response = DB::transaction(function () use ($evaluation, $employee, $assignment, $validationData) {
            $evalResponse = $evaluation->responses()->create([
                'employee_id' => $employee->id,
            ]);

            foreach ($evaluation->questions as $question) {
                $key = 'q_' . $question->id;
                $value = $validationData[$key] ?? null;

                if ($value !== null) {
                    $evalResponse->answers()->create([
                        'evaluation_question_id' => $question->id,
                        'answer_text'            => !in_array($question->type, ['scale', 'boolean']) ? (string) $value : null,
                        'answer_scale'           => $question->type === 'scale' ? (int) $value : null,
                        'answer_boolean'         => $question->type === 'boolean' ? (bool) $value : null,
                    ]);
                }
            }

            $assignment->update(['is_completed' => true]);

            return $evalResponse;
        });

        return response()->json([
            'success' => true,
            'message' => '¡Gracias! Tus respuestas han sido enviadas correctamente.',
            'data'    => [
                'response_id'   => $response->id,
                'evaluation_id' => $evaluation->id,
                'employee_id'   => $employee->id,
                'employee_name' => $employee->user ? $employee->user->name : null,
                'submitted_at'  => $response->created_at->toIso8601String(),
                'answers_count' => count(array_filter($validationData, fn($v) => $v !== null)),
            ]
        ], 201);
    }

    /**
     * Consultar respuestas históricas de una evaluación.
     */
    public function responses(Request $request, $id)
    {
        $evaluation = Evaluation::find($id);
        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluación no encontrada.',
            ], 404);
        }

        $employee = $this->resolveEmployee($request, $evaluation);
        $user = auth('sanctum')->user() ?? $request->user();

        $query = $evaluation->responses()->with(['employee.user', 'answers.question']);

        // Si es un empleado normal, solo ve sus propias respuestas
        if ($employee && (!$user || (!$user->isSuper() && !$user->isAdmin()))) {
            $query->where('employee_id', $employee->id);
        } elseif ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $responses = $query->latest()->get()->map(function ($resp) {
            return [
                'id'           => $resp->id,
                'submitted_at' => $resp->created_at?->toIso8601String(),
                'employee'     => [
                    'id'        => $resp->employee_id,
                    'id_number' => $resp->employee?->id_number,
                    'name'      => $resp->employee?->user?->name,
                ],
                'answers'      => $resp->answers->map(function ($ans) {
                    return [
                        'question_id'    => $ans->evaluation_question_id,
                        'question_text'  => $ans->question?->question_text,
                        'question_type'  => $ans->question?->type,
                        'answer_text'    => $ans->answer_text,
                        'answer_scale'   => $ans->answer_scale,
                        'answer_boolean' => $ans->answer_boolean,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'count'   => $responses->count(),
            'data'    => $responses,
        ]);
    }

    /**
     * Resolver el empleado a partir de Sanctum o de parámetros en la petición.
     */
    protected function resolveEmployee(Request $request, ?Evaluation $evaluation = null): ?Employee
    {
        $user = auth('sanctum')->user() ?? $request->user();

        // 1. Por ID explícito de empleado
        if ($request->filled('employee_id')) {
            $query = Employee::where('id', $request->employee_id);
            if ($evaluation) {
                $query->where('company_id', $evaluation->company_id);
            } elseif ($user && !$user->isSuper()) {
                $query->where('company_id', $user->company_id);
            }
            $emp = $query->first();
            if ($emp) {
                return $emp;
            }
        }

        // 2. Por cédula / documento de identidad (id_number)
        if ($request->filled('id_number')) {
            $query = Employee::where('id_number', $request->id_number);
            if ($evaluation) {
                $query->where('company_id', $evaluation->company_id);
            } elseif ($user && !$user->isSuper()) {
                $query->where('company_id', $user->company_id);
            }
            $emp = $query->first();
            if ($emp) {
                return $emp;
            }
        }

        // 3. Por correo electrónico (email)
        if ($request->filled('email')) {
            $userByEmail = User::where('email', $request->email)->first();
            if ($userByEmail && $userByEmail->employee) {
                if (!$evaluation || $userByEmail->company_id === $evaluation->company_id) {
                    return $userByEmail->employee;
                }
            }
        }

        // 4. Usuario autenticado con perfil de empleado asociado
        if ($user && $user->employee) {
            if (!$evaluation || $user->employee->company_id === $evaluation->company_id) {
                return $user->employee;
            }
        }

        return null;
    }

    /**
     * Extraer respuestas de múltiples formatos posibles para máxima compatibilidad con terceros:
     * - Formato estándar web: {"q_1": 8, "q_2": 1, ...}
     * - Formato lista JSON: {"answers": [{"question_id": 1, "value": 8}, ...]}
     * - Formato mapa JSON: {"answers": {"1": 8, "2": 1, ...}}
     */
    protected function extractAnswers(Request $request): array
    {
        $answers = [];

        // 1. answers como array o diccionario
        if ($request->has('answers')) {
            $rawAnswers = $request->input('answers');
            if (is_array($rawAnswers)) {
                foreach ($rawAnswers as $key => $item) {
                    if (is_array($item)) {
                        $qId = $item['question_id'] ?? $item['id'] ?? null;
                        $val = $item['value'] ?? $item['answer'] ?? null;
                        if ($qId !== null) {
                            $answers[(int) $qId] = $val;
                        }
                    } else {
                        if (is_numeric($key)) {
                            $answers[(int) $key] = $item;
                        }
                    }
                }
            }
        }

        // 2. campos directos estilo web q_{id}
        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'q_')) {
                $qId = substr($key, 2);
                if (is_numeric($qId)) {
                    $answers[(int) $qId] = $value;
                }
            }
        }

        return $answers;
    }
}
