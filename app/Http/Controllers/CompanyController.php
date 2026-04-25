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
            'name' => 'required|string|max:255',
            'rnc' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'srl_rate' => 'nullable|numeric|min:1.0|max:1.5',
        ]);

        Auth::user()->company->update($data);

        return back()->with('success', 'Información de empresa actualizada.');
    }
}
