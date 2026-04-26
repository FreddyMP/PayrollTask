@extends('layouts.app')

@section('title', 'Vista Previa del Documento')
@section('page-title', 'Resultado: ' . $template->title)

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="text-white mb-1">Vista Previa</h5>
            <p class="small">El documento ha sido generado con los valores correspondientes.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('documents.show', $template) }}" class="btn btn-outline-custom">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
            <form action="{{ route('documents.generate', $template) }}" method="POST">
                @csrf
                <input type="hidden" name="format" value="pdf">
                <!-- If we wanted to keep the employee_id, we'd need to pass it back or use session, 
                     but for now just a simple PDF download of the same template -->
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i>Descargar PDF
                </button>
            </form>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <div class="document-container bg-white text-dark p-5 rounded-1 shadow-sm" style="min-height: 800px; font-family: 'Times New Roman', Times, serif; line-height: 1.6;">
                    {!! nl2br(e($content)) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .document-container {
        border: 1px solid #ddd;
    }
</style>
@endsection
