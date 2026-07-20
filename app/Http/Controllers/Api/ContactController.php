<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;

class ContactController extends Controller
{
    /**
     * Send contact email.
     */
    public function send(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:50',
            'mensaje' => 'required|string',
        ]);

        $targetEmail = env('INCIDENTS_EMAIL_ADDRESS');

        if (!$targetEmail) {
            return response()->json([
                'success' => false,
                'message' => 'El correo de incidencias no está configurado en el servidor.'
            ], 500);
        }

        try {
            Mail::to($targetEmail)->send(new ContactMail(
                $request->nombre,
                $request->telefono,
                $request->mensaje
            ));

            return response()->json([
                'success' => true,
                'message' => 'Mensaje de contacto enviado correctamente.'
            ], 200);
        } catch (\Exception $e) {
            logger()->error('Error enviando correo de contacto API: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al enviar el mensaje de contacto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
