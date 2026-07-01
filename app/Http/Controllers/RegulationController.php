<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RegulationController extends Controller
{
    public function index()
    {
        $regulations = Regulation::where('company_id', Auth::user()->company_id)
            ->where('is_active', true)
            ->with('creator')
            ->orderBy('created_at', 'desc')
            ->get();

        $canManage = Auth::user()->isAdmin() || Auth::user()->isSuper();

        return view('regulations.index', compact('regulations', 'canManage'));
    }

    public function store(Request $request)
    {
        // Verificar que el usuario sea admin o super
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuper()) {
            return back()->with('error', 'No tienes permisos para crear reglamentos.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'required|file|mimes:pdf,doc,docx,txt|max:10240', // 10MB max
        ]);

        // Subir archivo
        $file = $request->file('file');
        $filePath = $file->store('regulations', 'public');
        $fileType = $file->getClientOriginalExtension();

        // Intentar extraer contenido según tipo
        $content = $this->extractContent($file, $fileType);

        Regulation::create([
            'company_id' => Auth::user()->company_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'file_type' => $fileType,
            'content' => $content,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('regulations.index')->with('success', 'Reglamento creado exitosamente.');
    }

    public function show(Regulation $regulation)
    {
        // Verificar que el reglamento pertenezca a la empresa del usuario
        if ($regulation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return view('regulations.show', compact('regulation'));
    }

    public function update(Request $request, Regulation $regulation)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuper()) {
            return back()->with('error', 'No tienes permisos para actualizar reglamentos.');
        }

        // Verificar que el reglamento pertenezca a la empresa del usuario
        if ($regulation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,txt|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        // Si hay nuevo archivo
        if ($request->hasFile('file')) {
            // Eliminar archivo anterior
            if ($regulation->file_path) {
                Storage::disk('public')->delete($regulation->file_path);
            }

            $file = $request->file('file');
            $filePath = $file->store('regulations', 'public');
            $fileType = $file->getClientOriginalExtension();
            $content = $this->extractContent($file, $fileType);

            $data['file_path'] = $filePath;
            $data['file_type'] = $fileType;
            $data['content'] = $content;
        }

        $regulation->update($data);

        return redirect()->route('regulations.index')->with('success', 'Reglamento actualizado exitosamente.');
    }

    public function destroy(Regulation $regulation)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuper()) {
            return back()->with('error', 'No tienes permisos para eliminar reglamentos.');
        }

        // Verificar que el reglamento pertenezca a la empresa del usuario
        if ($regulation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        // Eliminar archivo
        if ($regulation->file_path) {
            Storage::disk('public')->delete($regulation->file_path);
        }

        $regulation->delete();

        return redirect()->route('regulations.index')->with('success', 'Reglamento eliminado exitosamente.');
    }

    public function toggleStatus(Regulation $regulation)
    {
        // Verificar permisos
        if (!Auth::user()->isAdmin() && !Auth::user()->isSuper()) {
            return back()->with('error', 'No tienes permisos para cambiar el estado.');
        }

        // Verificar que el reglamento pertenezca a la empresa del usuario
        if ($regulation->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $regulation->update([
            'is_active' => !$regulation->is_active
        ]);

        $status = $regulation->is_active ? 'activado' : 'desactivado';
        return redirect()->route('regulations.index')->with('success', "Reglamento {$status} exitosamente.");
    }

    /**
     * Extrae contenido del archivo según su tipo
     */
    private function extractContent($file, $fileType)
    {
        try {
            $tempPath = $file->getRealPath();

            switch (strtolower($fileType)) {
                case 'txt':
                    return file_get_contents($tempPath);

                case 'pdf':
                    // Para PDF necesitarías una librería como smalot/pdfparser
                    // Por ahora retornamos null
                    return null;

                case 'doc':
                case 'docx':
                    // Para DOCX podrías usar phpoffice/phpword
                    // Por ahora retornamos null
                    return null;

                default:
                    return null;
            }
        } catch (\Exception $e) {
            \Log::error('Error extrayendo contenido: ' . $e->getMessage());
            return null;
        }
    }
}
