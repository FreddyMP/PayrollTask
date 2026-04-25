<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Hoja de Solicitud - {{ $applicationForm->company->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0; color: #666; font-size: 14px; }
        .form-section { margin-bottom: 25px; }
        .field-group { margin-bottom: 15px; }
        .field-label { font-weight: bold; font-size: 13px; display: block; margin-bottom: 5px; color: #555; }
        .field-value { border-bottom: 1px solid #ccc; height: 25px; margin-bottom: 10px; }
        .field-value-large { border: 1px solid #ccc; height: 80px; margin-bottom: 10px; }
        .table-field { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-field th, .table-field td { border: 1px solid #ccc; padding: 8px; font-size: 12px; text-align: left; }
        .table-field th { background-color: #f5f5f5; color: #333; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; font-size: 10px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .page-break { page-break-after: always; }
        .row { display: table; width: 100%; clear: both; }
        .col { display: table-cell; vertical-align: top; }
        .w-50 { width: 50%; }
        .pr-10 { padding-right: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hoja de Solicitud de Empleo</h1>
        <p>{{ $applicationForm->company->name }}</p>
        @if($applicationForm->company->phone)
            <p>Tel: {{ $applicationForm->company->phone }} | Email: {{ $applicationForm->company->email ?? 'N/A' }}</p>
        @endif
    </div>

    <div class="form-section">
        @foreach($applicationForm->fields as $field)
            <div class="field-group">
                <span class="field-label">{{ $field->label }}</span>
                
                @if($field->type === 'text' || $field->type === 'date' || $field->type === 'integer' || $field->type === 'decimal')
                    <div class="field-value"></div>
                @elseif($field->type === 'long_text')
                    <div class="field-value"></div>
                    <div class="field-value"></div>
                @elseif($field->type === 'textarea')
                    <div class="field-value-large"></div>
                @elseif($field->type === 'table')
                    <table class="table-field">
                        <thead>
                            <tr>
                                @foreach($field->options['columns'] ?? [] as $column)
                                    <th>{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i < 4; $i++)
                                <tr>
                                    @foreach($field->options['columns'] ?? [] as $column)
                                        <td style="height: 20px;"></td>
                                    @endforeach
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                @endif
            </div>
        @endforeach
    </div>

    <div class="footer">
        Este documento es una hoja de solicitud oficial de {{ $applicationForm->company->name }}. 
        Generado el {{ date('d/m/Y H:i') }}
    </div>
</body>
</html>
