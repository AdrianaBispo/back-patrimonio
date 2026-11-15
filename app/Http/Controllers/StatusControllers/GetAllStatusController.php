<?php

namespace App\Http\Controllers\StatusControllers;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\Request;

class GetAllStatusController extends Controller
{
    public function __invoke(Request $request)
    {
        $status = Status::query()
            ->select('id', 'nome')
            ->orderBy('id')
            ->get();

        return response()->json(['data' => $status]);
    }
}
