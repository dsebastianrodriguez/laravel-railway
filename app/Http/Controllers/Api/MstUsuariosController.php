<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstUsuarios;
use Illuminate\Http\Request;

class MstUsuariosController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/usuarios",
     *     tags={"Maestro_usuarios"},
     *     summary="Obtiene el listado de usuarios",
     *     description="Retorna una lista de todos los usuarios. Requiere un token de autenticación Bearer.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="johndoe@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     example="3112112111"
     *                 ),
     *                 @OA\Property(
     *                     property="language",
     *                     type="string",
     *                     example="Spanish"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2024-02-23T00:09:16.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2024-02-23T12:33:45.000000Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=401
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $usuario = MstUsuarios::all();
        if ($usuario->isEmpty()) {
            $data = [
                'message' => 'No data found',
                'status' => 200
            ];
            return  response()->json($data, 200);
        } else {
            return response()->json($usuario, 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/profile/{login}",
     *     tags={"Maestro_usuarios"},
     *     summary="Obtiene un usuario por login",
     *     description="Retorna un usuario basado en su login. Requiere un token de autenticación Bearer.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="login",
     *         in="path",
     *         description="Login del usuario",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(
     *                     property="id",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="John Doe"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="johndoe@example.com"
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     example="3112112111"
     *                 ),
     *                 @OA\Property(
     *                     property="language",
     *                     type="string",
     *                     example="Spanish"
     *                 ),
     *                 @OA\Property(
     *                     property="created_at",
     *                     type="string",
     *                     example="2024-02-23T00:09:16.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2024-02-23T12:33:45.000000Z"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No data found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No data found"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=404
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthorized"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=401
     *             )
     *         )
     *     )
     * )
     */
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
