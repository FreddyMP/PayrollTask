<?php

namespace App\Http\Controllers;

use App\Models\CompanyField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyFieldController extends Controller
{
    public function index()
    {
        $fields = CompanyField::where('company_id', Auth::user()->company_id)->get();
        return view('company.fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'nullable|string',
        ]);

        CompanyField::create([
            'company_id' => Auth::user()->company_id,
            'name' => $request->name,
            'value' => $request->value,
            'is_bold' => $request->has('is_bold'),
        ]);

        return redirect()->back()->with('success', 'Variable guardada correctamente.');
    }

    public function update(Request $request, CompanyField $field)
    {
        $this->authorizeAccess($field);

        $request->validate([
            'name' => 'required|string|max:255',
            'value' => 'nullable|string',
        ]);

        $field->update([
            'name' => $request->name,
            'value' => $request->value,
            'is_bold' => $request->has('is_bold'),
        ]);

        return redirect()->back()->with('success', 'Variable actualizada.');
    }

    public function destroy(CompanyField $field)
    {
        $this->authorizeAccess($field);
        $field->delete();
        return redirect()->back()->with('success', 'Variable eliminada.');
    }

    private function authorizeAccess(CompanyField $field)
    {
        if ($field->company_id !== Auth::user()->company_id) {
            abort(403);
        }
    }
}
