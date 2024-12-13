<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;

class AdminAuthController extends Controller
{
    /**
     * Maneja el inicio de sesión de un administrador.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Buscar el administrador por el username
        $admin = Admin::where('username', $request->username)->first();

        // Validar si el administrador existe y la contraseña es correcta
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['error' => 'Credenciales inválidas'], 401);
        }

        // Generar un token JWT para el administrador
        $token = JWTAuth::fromUser($admin);

        // Retornar el token y los datos del administrador
        return response()->json([
            'message' => 'Inicio de sesión exitoso',
            'token' => $token,
            'admin' => [
                'id' => $admin->id,
                'username' => $admin->username,
                'email' => $admin->email,
            ]
        ]);
    }

    /**
     * Cierra la sesión invalidando el token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        // Invalidar el token
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Sesión cerrada correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al cerrar sesión'], 500);
        }
    }

    /**
     * Obtiene los datos del administrador autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $admin = JWTAuth::user();
        return response()->json(['admin' => $admin]);
    }
}
