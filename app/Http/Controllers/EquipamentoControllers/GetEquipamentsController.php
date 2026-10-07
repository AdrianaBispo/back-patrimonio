<?php

namespace App\Http\Controllers\EquipamentoControllers;

use Illuminate\Http\Request;
use App\Models\Equipamento;
use App\Http\Controllers\Controller;


class GetEquipamentsController extends Controller
{
    public function __invoke(Request $request)
    {
        $name = $request['name'];
        $serie = $request['serie'];
        $perPage = (int) $request['per_page'] ?? 0;
        if ($name) {
            $equipaments = Equipamento::where('name', 'like', '%' . $name . '%')->get()->paginate($perPage);
        } elseif ($serie) {
            $equipaments = Equipamento::where('serie', 'like', '%' . $serie . '%')->get()->paginate($perPage);
        } elseif ($name && $serie) {
            $equipaments = Equipamento::where('name', 'like', '%' . $name . '%')->where('serie', 'like', '%' . $serie . '%')->get()->paginate($perPage);
        }
        else {
            $equipaments = Equipamento::all()->paginate($perPage);
        } 
        
        return response()->json($equipaments);
    }
}
