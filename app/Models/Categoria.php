<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\TipoCategoria;
use Override;

class Categoria extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'categorias';

    protected $fillable = ['nome', 'tipo', 'nota'];

    #[Override]
    protected function casts():array
    {
        return 
        [
            'tipo' => TipoCategoria::class,
        ];
    }
}
