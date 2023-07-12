<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request){
      
        
        $this->validateLogin($request);

        if(Auth::attempt($request->only('email', 'password'))){
            $user = $request->user();
            $token =  $user->createToken($request->email)->plainTextToken;
            return response()->json([
                'token'=>$token,
                'email'=>$user->email,
                'name'=>$user->name,
                'message'=>'Success'
            ]);
        }

        return response()->json([
            'message'=>'Correo o Contrasena Invalida'
        ], 401);
    }

    public function validateLogin(Request $request){
        return $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);
    }

}
