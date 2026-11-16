<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoricEquipaments extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'historic_equipaments';

    protected $fillable = ['equipament_id', 'status_id', 'user_id'];

    public function equipament()
    {
        return $this->belongsTo(Equipamento::class, 'equipament_id', 'eqi_id');
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
