<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Registro de un nuevo usuario
     * @OA\Post (
     *     path="/api/auth",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 example="John Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="johndoe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
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
     *                     property="created_at",
     *                     type="string",
     *                     example="2024-05-21T14:30:00.000000Z"
     *                 ),
     *                 @OA\Property(
     *                     property="updated_at",
     *                     type="string",
     *                     example="2024-05-21T14:30:00.000000Z"
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJh..."
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=201
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error in validation of data",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error in validation of data"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=400
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error creating the user",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Error creating the user"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error in validation of data',
                'errors' => $validator->errors(),
                'status' => 400
            ];

            return  response()->json($data, 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if (!$user) {
            $data = [
                'message' => 'Error creating the user',
                'status' => 500
            ];
            return  response()->json($data, 500);
        }

        $token = JWTAuth::fromUser($user);

        $data = [
            'user' => $user,
            'token' => $token,
            'status' => 201
        ];

        return   response()->json($data, 201);
    }

    /**
     * Inicio de sesión del usuario
     * @OA\Post (
     *     path="/api/login",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 example="johndoe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 example="password123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="eyJ0eXAiOiJKV1QiLCJh..."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="invalid credentials"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=400
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Not create token",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Not create token"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */
    public function login(LoginRequest $request)
    {
        $credencials =  $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credencials)) {
                $data = [
                    'error' => 'invalid credentials',
                    'status' => 400
                ];
                return  response()->json($data, 400);
            }

            $user = auth()->user();

            $customClaims = ['user' => $user->name, 'administrador' => 'SI', 'fecha_vencimiento' => '2024-04-26'];

            $token = JWTAuth::claims($customClaims)->attempt($credencials);
        } catch (JWTException $e) {
            $data = [
                'error' => 'Not create token',
                'status' => 500
            ];
            return response()->json([$data, 500]);
        }

        return response()->json(compact('token'));
    }

    /**
     * Cierre de sesión del usuario
     * @OA\Post (
     *     path="/api/logout",
     *     tags={"Auth"},
     *      @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="token",
     *                 type="string",
     *                 example="johndoe@example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token invalidated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Token invalidated successfully"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=200
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to invalidate token",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Failed to invalidate token"
     *             ),
     *             @OA\Property(
     *                 property="status",
     *                 type="integer",
     *                 example=500
     *             )
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {

            $token = JWTAuth::getToken();


            JWTAuth::invalidate($token);

            return response()->json(['message' => 'Token invalidated successfully', 'status' => 200]);
        } catch (JWTException $e) {

            return response()->json(['message' => 'Failed to invalidate token', 'status' => 500], 500);
        } catch (\Exception $e) {

            return response()->json(['message' => 'An unexpected error occurred', 'status' => 500], 500);
        }
    }
}
