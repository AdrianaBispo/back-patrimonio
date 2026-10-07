<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoricoEquipamento extends Model
{
    use hasFactory;
    protected $primaryKey = 'id';
    protected $table = 'historico_equipamentos';

    protected $fillable = ['equipamento_id', 'status_id', 'usuario_id', 'data_hora'];

    public function equipamento()
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }
}
