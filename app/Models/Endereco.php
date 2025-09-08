<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Endereco extends Model
{
    use HasFactory;

    protected $fillable = [
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cep',
        'nome_cidade',
        'estado',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'endereco_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'address_id');
    }
}