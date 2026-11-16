<?php

namespace App\Http\Controllers\EquipamentoControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EquipamentoImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ],
        [
            'image.required' => 'A imagem é obrigatória',
            'image.image' => 'O arquivo deve ser uma imagem',
            'image.mimes' => 'O arquivo deve ser uma imagem JPEG, PNG ou JPG',
            'image.max' => 'O arquivo deve ter no máximo 5MB',
        ]);

        // Store file inside storage/app/public/uploads
        $imagePath = $request->file('image')->store('public/uploads', 'public');
     
        if ($imagePath) {
            return response()->json([
                'message' => 'Imagem enviada com sucesso',
                'imagepath' => $imagePath,
            ]);
        } else {
            return response()->json([
                'message' => 'Erro ao enviar imagem',
            ], 500);
        }
    }
}
