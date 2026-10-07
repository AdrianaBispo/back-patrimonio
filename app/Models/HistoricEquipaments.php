<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HistoricEquipaments extends Model
{
    use hasFactory;
    protected $primaryKey = 'id';
    protected $table = 'historic_equipaments';

    protected $fillable = ['equipament_id', 'status_id', 'user_id', 'date_time'];

    public function equipament()
    {
        return $this->belongsTo(Equipamento::class);
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
