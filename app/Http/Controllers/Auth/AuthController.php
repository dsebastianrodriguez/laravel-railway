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
    public function register(Request $request){
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

    public function login(LoginRequest $request){
        $credencials =  $request->only('email','password');
        try {
            if(!$token = JWTAuth::attempt($credencials)){
                $data = [
                    'error' => 'invalid credentials',
                    'status' => 400
                ];
                return  response()->json($data, 400);
            }
            
            $user = auth()->user();
    
            $customClaims = ['user' => $user->name,'administrador' => 'SI','fecha_vencimiento' => '2024-04-26'];
    
            $token = JWTAuth::claims($customClaims)->attempt($credencials);
    
        } catch(JWTException $e){
            $data = [
                'error' => 'Not create token',
                'status' => 500
            ];
            return response()->json([$data,500]);
        }
    
        return response()->json(compact('token'));
    }

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
