<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\EvaluationQuestion;
use App\Models\EvaluationAssignment;
use App\Models\EvaluationResponse;
use App\Models\EvaluationAnswer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EvaluationApiTest extends TestCase
{
    use RefreshDatabase;

    protected string $authHeader;
    protected Company $company;
    protected User $user;
    protected Employee $employee;
    protected Evaluation $evaluation;
    protected EvaluationQuestion $qScale;
    protected EvaluationQuestion $qBoolean;
    protected EvaluationQuestion $qText;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authHeader = env('X_AUTHORIZATION', '95b0602e53dc2ac17cdc85b968180a09');

        // Setup test company, user, and employee
        $this->company = Company::firstOrCreate(
            ['email' => 'testcompany@example.com'],
            ['name' => 'Empresa Test', 'status' => 'active']
        );

        $this->user = User::firstOrCreate(
            ['email' => 'empleadotest@example.com'],
            [
                'name' => 'Empleado Test',
                'password' => bcrypt('password'),
                'company_id' => $this->company->id,
                'role' => 'usuario',
                'status' => 'active',
            ]
        );

        $this->employee = Employee::firstOrCreate(
            ['user_id' => $this->user->id],
            [
                'company_id' => $this->company->id,
                'id_number' => 'TEST123456',
            ]
        );

        // Create evaluation
        $this->evaluation = Evaluation::create([
            'company_id' => $this->company->id,
            'title' => 'Evaluación Test API',
            'description' => 'Descripción de prueba',
            'status' => 'active',
            'allow_multiple_responses' => false,
        ]);

        // Questions
        $this->qScale = $this->evaluation->questions()->create([
            'question_text' => '¿Calificación general?',
            'type' => 'scale',
            'order' => 1,
            'is_required' => true,
        ]);

        $this->qBoolean = $this->evaluation->questions()->create([
            'question_text' => '¿Recomiendas la empresa?',
            'type' => 'boolean',
            'order' => 2,
            'is_required' => true,
        ]);

        $this->qText = $this->evaluation->questions()->create([
            'question_text' => 'Comentario adicional',
            'type' => 'text',
            'order' => 3,
            'is_required' => false,
        ]);
    }

    public function test_api_rejects_missing_x_authorization(): void
    {
        $response = $this->getJson("/api/evaluations/{$this->evaluation->id}");
        $response->assertStatus(401);
    }

    public function test_get_evaluation_details_with_questions(): void
    {
        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->getJson("/api/evaluations/{$this->evaluation->id}?employee_id={$this->employee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $this->evaluation->id,
                    'title' => 'Evaluación Test API',
                    'status' => 'active',
                ],
            ]);

        $this->assertCount(3, $response->json('data.questions'));
    }

    public function test_submit_rejects_unassigned_employee(): void
    {
        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                'answers' => [
                    ['question_id' => $this->qScale->id, 'value' => 8],
                    ['question_id' => $this->qBoolean->id, 'value' => 1],
                ],
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'El empleado no está asignado a esta evaluación.',
            ]);
    }

    public function test_submit_validates_required_and_scale_limits(): void
    {
        // Assign employee
        $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
        ]);

        // Submit with scale out of bounds (15) and boolean missing
        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                'answers' => [
                    ['question_id' => $this->qScale->id, 'value' => 15],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonStructure(['success', 'message', 'errors']);
    }

    public function test_successful_evaluation_submission_and_completion(): void
    {
        $assignment = $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
            'is_completed' => false,
        ]);

        // Submit valid answers
        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'id_number' => $this->employee->id_number,
                'answers' => [
                    ['question_id' => $this->qScale->id, 'value' => 9],
                    ['question_id' => $this->qBoolean->id, 'value' => true],
                    ['question_id' => $this->qText->id, 'value' => 'Excelente proceso'],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => '¡Gracias! Tus respuestas han sido enviadas correctamente.',
            ]);

        // Verify assignment marked completed
        $this->assertTrue($assignment->fresh()->is_completed);

        // Verify EvaluationResponse and Answers in database
        $this->assertDatabaseHas('evaluation_responses', [
            'evaluation_id' => $this->evaluation->id,
            'employee_id' => $this->employee->id,
        ]);

        $this->assertDatabaseHas('evaluation_answers', [
            'evaluation_question_id' => $this->qScale->id,
            'answer_scale' => 9,
        ]);

        $this->assertDatabaseHas('evaluation_answers', [
            'evaluation_question_id' => $this->qBoolean->id,
            'answer_boolean' => 1,
        ]);

        $this->assertDatabaseHas('evaluation_answers', [
            'evaluation_question_id' => $this->qText->id,
            'answer_text' => 'Excelente proceso',
        ]);

        // Try submitting again when allow_multiple_responses is false
        $secondResponse = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                'answers' => [
                    ['question_id' => $this->qScale->id, 'value' => 10],
                    ['question_id' => $this->qBoolean->id, 'value' => 1],
                ],
            ]);

        $secondResponse->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => 'Ya has completado esta evaluación.',
            ]);
    }

    public function test_submit_with_web_format_and_allow_multiple_responses(): void
    {
        $this->evaluation->update(['allow_multiple_responses' => true]);

        $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
            'is_completed' => false,
        ]);

        // Submit using q_X format (same as web form)
        $response1 = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                "q_{$this->qScale->id}" => 7,
                "q_{$this->qBoolean->id}" => 0,
                "q_{$this->qText->id}" => 'Primer envío',
            ]);

        $response1->assertStatus(201);

        // Since allow_multiple_responses is true, submit again
        $response2 = $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                'answers' => [
                    (string) $this->qScale->id => 10,
                    (string) $this->qBoolean->id => 1,
                ],
            ]);

        $response2->assertStatus(201);
    }

    public function test_submit_with_authenticated_sanctum_token(): void
    {
        $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
            'is_completed' => false,
        ]);

        $token = $this->user->createToken('test_token')->plainTextToken;

        // Submitting with Sanctum Bearer token without needing employee_id in body
        $response = $this->withHeaders([
            'x-authorization' => $this->authHeader,
            'Authorization' => 'Bearer ' . $token,
        ])->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
            'answers' => [
                ['question_id' => $this->qScale->id, 'value' => 10],
                ['question_id' => $this->qBoolean->id, 'value' => true],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'employee_id' => $this->employee->id,
                ],
            ]);
    }

    public function test_get_employee_assigned_evaluations_list(): void
    {
        $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
            'is_completed' => false,
        ]);

        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->getJson("/api/evaluations?employee_id={$this->employee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ]);

        $this->assertEquals($this->evaluation->id, $response->json('data.0.id'));
        $this->assertTrue($response->json('data.0.can_submit'));
    }

    public function test_get_evaluation_responses_history(): void
    {
        $this->evaluation->assignments()->create([
            'employee_id' => $this->employee->id,
            'is_completed' => false,
        ]);

        $this->withHeader('x-authorization', $this->authHeader)
            ->postJson("/api/evaluations/{$this->evaluation->id}/submit", [
                'employee_id' => $this->employee->id,
                'answers' => [
                    ['question_id' => $this->qScale->id, 'value' => 8],
                    ['question_id' => $this->qBoolean->id, 'value' => 1],
                ],
            ]);

        $response = $this->withHeader('x-authorization', $this->authHeader)
            ->getJson("/api/evaluations/{$this->evaluation->id}/responses?employee_id={$this->employee->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'count' => 1,
            ]);

        $this->assertCount(2, $response->json('data.0.answers'));
    }
}
