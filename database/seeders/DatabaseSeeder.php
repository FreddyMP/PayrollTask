<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use App\Models\Employee;
use App\Models\Task;
use App\Models\AccessLog;
use App\Models\UserRequest;
use App\Models\Payroll;
use App\Models\Expense;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo company
        $company = Company::create([
            'name' => 'TechCorp Solutions',
            'rnc' => '131-12345-6',
            'email' => 'info@techcorp.com',
            'phone' => '809-555-0100',
            'address' => 'Av. Winston Churchill #100, Santo Domingo',
            'plan' => 'professional',
            'status' => 'active',
        ]);

        // Create Super user
        $super = User::create([
            'company_id' => $company->id,
            'name' => 'Carlos Méndez',
            'email' => 'admin@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'super',
            'phone' => '809-555-0101',
            'position' => 'CEO',
            'status' => 'active',
        ]);

        // Create Admin
        $admin = User::create([
            'company_id' => $company->id,
            'name' => 'María García',
            'email' => 'maria@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '809-555-0102',
            'position' => 'Gerente de RRHH',
            'status' => 'active',
        ]);

        // Create Supervisor
        $supervisor = User::create([
            'company_id' => $company->id,
            'name' => 'Juan Pérez',
            'email' => 'juan@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'supervisor',
            'phone' => '809-555-0103',
            'position' => 'Project Manager',
            'status' => 'active',
        ]);

        // Create regular users
        $dev1 = User::create([
            'company_id' => $company->id,
            'name' => 'Ana Rodríguez',
            'email' => 'ana@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'usuario',
            'phone' => '809-555-0104',
            'position' => 'Desarrolladora Frontend',
            'status' => 'active',
        ]);

        $dev2 = User::create([
            'company_id' => $company->id,
            'name' => 'Pedro Martínez',
            'email' => 'pedro@techcorp.com',
            'password' => Hash::make('password123'),
            'role' => 'usuario',
            'phone' => '809-555-0105',
            'position' => 'Diseñador UI/UX',
            'status' => 'active',
        ]);

        // Create employees
        $allUsers = [$super, $admin, $supervisor, $dev1, $dev2];
        $departments = ['Dirección', 'RRHH', 'Desarrollo', 'Desarrollo', 'Diseño'];
        $salaries = [150000, 85000, 95000, 75000, 65000];

        foreach ($allUsers as $i => $user) {
            Employee::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'department' => $departments[$i],
                'salary' => $salaries[$i],
                'hire_date' => now()->subMonths(rand(6, 36)),
                'contract_type' => 'full_time',
                'id_number' => '001-' . str_pad($i + 1, 7, '0', STR_PAD_LEFT) . '-' . rand(1, 9),
            ]);
        }

        // Create tasks
        $taskData = [
            ['title' => 'Rediseñar landing page', 'status' => 'completed', 'priority' => 'high', 'assigned_to' => $dev2->id],
            ['title' => 'Implementar API de pagos', 'status' => 'in_progress', 'priority' => 'urgent', 'assigned_to' => $dev1->id],
            ['title' => 'Revisar documentación técnica', 'status' => 'review', 'priority' => 'medium', 'assigned_to' => $supervisor->id],
            ['title' => 'Configurar CI/CD pipeline', 'status' => 'pending', 'priority' => 'high', 'assigned_to' => $dev1->id],
            ['title' => 'Diseñar dashboard analytics', 'status' => 'pending', 'priority' => 'medium', 'assigned_to' => $dev2->id],
            ['title' => 'Optimizar queries de BD', 'status' => 'in_progress', 'priority' => 'low', 'assigned_to' => $dev1->id],
            ['title' => 'Migrar servidor producción', 'status' => 'pending', 'priority' => 'urgent', 'assigned_to' => $supervisor->id],
            ['title' => 'Testing módulo de reportes', 'status' => 'review', 'priority' => 'medium', 'assigned_to' => $dev1->id],
        ];

        foreach ($taskData as $t) {
            Task::create(array_merge($t, [
                'company_id' => $company->id,
                'created_by' => $supervisor->id,
                'description' => 'Descripción de la tarea: ' . $t['title'],
                'due_date' => now()->addDays(rand(1, 30)),
            ]));
        }

        // Create access logs
        foreach ($allUsers as $user) {
            for ($d = 5; $d >= 0; $d--) {
                AccessLog::create([
                    'user_id' => $user->id,
                    'login_at' => now()->subDays($d)->setHour(rand(7, 9))->setMinute(rand(0, 59)),
                    'logout_at' => now()->subDays($d)->setHour(rand(17, 19))->setMinute(rand(0, 59)),
                    'ip_address' => '192.168.1.' . rand(10, 200),
                ]);
            }
        }

        // Create requests
        UserRequest::create([
            'user_id' => $dev1->id,
            'company_id' => $company->id,
            'type' => 'vacation',
            'status' => 'pending',
            'start_date' => now()->addDays(15),
            'end_date' => now()->addDays(22),
            'description' => 'Vacaciones familiares programadas.',
        ]);

        UserRequest::create([
            'user_id' => $dev2->id,
            'company_id' => $company->id,
            'type' => 'permission',
            'status' => 'approved',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(3),
            'description' => 'Cita médica.',
            'reviewed_by' => $supervisor->id,
            'reviewed_at' => now(),
        ]);

        UserRequest::create([
            'user_id' => $supervisor->id,
            'company_id' => $company->id,
            'type' => 'work_letter',
            'status' => 'pending',
            'description' => 'Carta de trabajo para trámite bancario.',
        ]);

        // Create payrolls
        $employees = Employee::where('company_id', $company->id)->get();
        foreach (['2024-01', '2024-02', '2024-03'] as $period) {
            foreach ($employees as $emp) {
                $gross = $emp->salary;
                $deductions = $gross * 0.12;
                Payroll::create([
                    'employee_id' => $emp->id,
                    'company_id' => $company->id,
                    'period' => $period,
                    'gross_salary' => $gross,
                    'deductions' => $deductions,
                    'net_salary' => $gross - $deductions,
                    'payment_date' => now(),
                    'status' => 'paid',
                ]);
            }
        }

        // Create expenses
        $categories = ['Nómina', 'Servicios', 'Equipos', 'Marketing', 'Oficina'];
        for ($i = 0; $i < 10; $i++) {
            Expense::create([
                'company_id' => $company->id,
                'category' => $categories[array_rand($categories)],
                'amount' => rand(5000, 50000),
                'description' => 'Gasto operativo mensual #' . ($i + 1),
                'expense_date' => now()->subDays(rand(0, 90)),
                'created_by' => $admin->id,
            ]);
        }
    }
}
