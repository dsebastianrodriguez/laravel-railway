<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstUsuarios;
use Illuminate\Http\Request;

class MstUsuariosController extends Controller
{
    public function index()
    {
        $usuario = MstUsuarios::all();
        if ($usuario->isEmpty()) {
            $data = [
                'message' => 'No data found',
                'status' => 200
            ];
            return  response()->json($data,200);
        } else {
            return response()->json($usuario, 200);
        }
    }

    public function show($login)
    {
        $usuario = MstUsuarios::where('login', $login)->first();
        if (!$usuario) {
            $data = [
                'message' => 'No data found',
                'status' => 404
            ];
            return  response()->json($data, 404);
        } else {
            unset($usuario['password']);
            $data = [
                'user' => $usuario,
                'status' => 200
            ];
            return response()->json($data, 200);
        }
    }
}
