<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferenteDatosBancarios extends Model
{
    protected $table = 'referente_datos_bancarios';

    protected $fillable = [
        'referente_id',
        'banco',
        'tipo_cuenta',
        'numero_cuenta',
    ];

    public function referente()
    {
        return $this->belongsTo(Referente::class, 'referente_id');
    }
}
