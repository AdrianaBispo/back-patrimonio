<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Equipamento extends Model
{
    use HasFactory, HasUuids;
    protected $primaryKey = 'eqi_id';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $table = 'equipament';

    protected $fillable = [
        'eqi_name',
        'eqi_descricao',
        'eqi_serie',
        'eqi_status_id',
        'imagem_url',
        'usuario_id',
        'categoria_id',
        'tipo_equipamento_id',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function tipoEquipamento()
    {
        return $this->belongsTo(TipoEquipamento::class);
    }
}
