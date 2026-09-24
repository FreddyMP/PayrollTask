<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Referente extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nombre_completo',
        'cedula_rnc',
        'telefono',
        'password',
        'codigo_referido',
        'status',
        'primera_vez',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'    => 'hashed',
            'primera_vez' => 'boolean',
        ];
    }

    /**
     * Companies that registered using this referente's code.
     */
    public function companies()
    {
        return $this->hasMany(Company::class, 'referente_id');
    }

    /**
     * Banking information for this referente.
     */
    public function datosBancarios()
    {
        return $this->hasOne(ReferenteDatosBancarios::class, 'referente_id');
    }

    /**
     * Generate a unique referral code based on name + random string.
     */
    public static function generateCode(string $nombre): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $nombre), 0, 4));
        do {
            $code = $prefix . strtoupper(substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 4));
        } while (self::where('codigo_referido', $code)->exists());

        return $code;
    }
}
