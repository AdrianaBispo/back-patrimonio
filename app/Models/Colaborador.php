<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colaborador extends Model{
    use HasFactory, HasUuids;

    protected $table = 'colaboradores';

    protected $fillable = ['nome', 'email', 'celular', 'departamento_id'];


    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}