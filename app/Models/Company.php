<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'rnc', 'email', 'phone', 'address', 'logo', 'plan', 'status',
        'saturday_rest', 'sunday_rest', 'srl_rate', 'payroll_frequency',
        'bonus_payment_method', 'bonus_biweekly_split',
        'subscription_plan', 'subscription_selected_at',
    ];

    protected $casts = [
        'subscription_selected_at' => 'datetime',
    ];

    /**
     * Features allowed per plan.
     * Trial period (< 90 days): all features enabled.
     */
    private static array $planFeatures = [
        'starter' => [
            'dashboard', 'payroll', 'employees', 'settings', 'company',
            'incidents', 'access_logs', 'departments',
        ],
        'growth' => [
            'dashboard', 'payroll', 'employees', 'settings', 'company',
            'incidents', 'access_logs', 'departments',
            'org_chart', 'fichaje', 'requests', 'calendar', 'regulations', 'vacations',
        ],
        'business' => [
            'dashboard', 'payroll', 'employees', 'settings', 'company',
            'incidents', 'access_logs', 'departments',
            'org_chart', 'fichaje', 'requests', 'calendar', 'regulations', 'vacations',
            'reports', 'evaluations', 'projects', 'tasks',
        ],
        'enterprise' => [
            'dashboard', 'payroll', 'employees', 'settings', 'company',
            'incidents', 'access_logs', 'departments',
            'org_chart', 'fichaje', 'requests', 'calendar', 'regulations', 'vacations',
            'reports', 'evaluations', 'projects', 'tasks',
            'recruitment', 'contractors', 'documents', 'devices',
        ],
    ];

    /**
     * Employee limits per plan.
     */
    private static array $planLimits = [
        'starter'    => 10,
        'growth'     => 25,
        'business'   => 50,
        'enterprise' => 100,
    ];

    /**
     * Check if the company's trial period has expired (> 90 days since creation).
     */
    public function isTrialExpired(): bool
    {
        return $this->created_at->lt(now()->subDays(90));
    }

    /**
     * Check if the company needs to select a subscription plan (trial expired and no plan chosen).
     */
    public function needsSubscription(): bool
    {
        return $this->isTrialExpired() && empty($this->subscription_plan);
    }

    /**
     * Check if the company has access to a given feature.
     * During trial (< 90 days), all features are allowed.
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->isTrialExpired()) {
            return true; // Full access during trial
        }

        $plan = $this->subscription_plan;

        if (!$plan || !isset(self::$planFeatures[$plan])) {
            return false;
        }

        return in_array($feature, self::$planFeatures[$plan]);
    }

    /**
     * Get employee limit for the current plan.
     * During trial, use enterprise limit (100).
     */
    public function getEmployeeLimit(): int
    {
        if (!$this->isTrialExpired()) {
            return 100;
        }

        return self::$planLimits[$this->subscription_plan] ?? 0;
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function requests()
    {
        return $this->hasMany(UserRequest::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function regulations()
    {
        return $this->hasMany(Regulation::class);
    }
}
