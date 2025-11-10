<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login do usuário
     */
    public function login(Request $request)
    {
        $credenciais = $request->only(['email', 'password']);

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $token = Auth::guard('api')->attempt($credenciais);

        if (!$token) {
            return response()->json([
                'error' => 'Credenciais inválidas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Logout do usuário (invalida o token)
     */
    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Refresh do token (gera um novo token)
     */
    public function refresh()
    {
        // Usando JWTAuth facade diretamente
        $token = JWTAuth::refresh(JWTAuth::getToken());

        return $this->respondWithToken($token);
    }

    /**
     * Retorna os dados do usuário autenticado
     */
    public function me()
    {
        return response()->json(Auth::guard('api')->user());
    }

    /**
     * Formata a resposta com o token
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 7200,
            'user' => Auth::guard('api')->user()
        ]);
    }
}
