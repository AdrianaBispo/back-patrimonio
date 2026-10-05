<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
#[Fillable(['nome'])]
class Status extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'status';

    protected $fillable = ['nome'];

}
