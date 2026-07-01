<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company;
        return view('company.edit', compact('company'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'rnc'               => 'nullable|string|max:20',
            'email'             => 'nullable|email',
            'phone'             => 'nullable|string|max:20',
            'address'           => 'nullable|string',
            'srl_rate'          => 'nullable|numeric|min:1.0|max:1.5',
            'payroll_frequency' => 'nullable|in:monthly,biweekly',
            'bonus_payment_method' => 'nullable|in:payroll,separate',
            'bonus_biweekly_split' => 'nullable|in:both,q1,q2',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048|dimensions:max_width=600,max_height=500',
        ]);

        $company = Auth::user()->company;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($company->logo) {
                \Storage::disk('s3')->delete($company->logo);
            }

            // Store new logo on S3
            $logoPath = $request->file('logo')->store('company-logos', 's3');
            $data['logo'] = $logoPath;
        }

        $company->update($data);

        return back()->with('success', 'Información de empresa actualizada.');
    }

    /**
     * Actualiza la configuración de nómina y bonificaciones desde el modal del dashboard.
     * Solo accesible para usuarios con rol "super".
     */
    public function updatePayrollFrequency(Request $request)
    {
        $data = $request->validate([
            'payroll_frequency' => 'required|in:monthly,biweekly',
            'bonus_payment_method' => 'required|in:payroll,separate',
            'bonus_biweekly_split' => 'nullable|in:both,q1,q2',
        ]);

        Auth::user()->company->update($data);

        return redirect()->route('dashboard')->with('success', 'Configuración de nómina guardada correctamente.');
    }

    public function deleteLogo()
    {
        $company = Auth::user()->company;

        if ($company->logo) {
            \Storage::disk('s3')->delete($company->logo);
            $company->update(['logo' => null]);
        }

        return back()->with('success', 'Logo eliminado exitosamente.');
    }
}
