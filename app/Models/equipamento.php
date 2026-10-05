<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Attributes\Fillable;

class Equipamento extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'equipamentos';

    protected $fillable = ['nome', 'descricao', 'serie', 'status_id', 'imagem_url', 'usuario_id'];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
