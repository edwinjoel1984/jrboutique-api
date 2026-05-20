<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $this->validateLogin($request);
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = $request->user();
            $token = $user->createToken($request->email)->plainTextToken;
            return response()->json([
                'body' => [
                    'token'             => $token,
                    'user_id'           => $user->id,
                    'role_id'           => $user->role_id,
                    'name'              => $user->name,
                    'email'             => $user->email,
                    'printer_tunnel_url' => $user->printer_tunnel_url,
                ]
            ]);
        }

        return response()->json([
            'message' => 'Correo o Contrasena Invalida'
        ], 422);
    }

    public function validateLogin(Request $request)
    {
        return $request->validate([
            'email'    => 'required|string',
            'password' => 'required',
        ]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesion finalizada correctamente'
        ], 200);
    }
}
