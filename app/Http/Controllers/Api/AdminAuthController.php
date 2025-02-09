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
        ],200);
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
    {   try {
            $admin = JWTAuth::user();
            return response()->json(['admin' => $admin],200);
        }catch(\Exception $e) {
            return response()->json([ 'message' => 'No autorizado'],401); 
        }
    }
    
    public function updatePassword(Request $request)
    {
        try {
            // Obtener el usuario autenticado desde el token JWT
            $admin = JWTAuth::user();

            if (!$admin) {
                return response()->json([
                    'message' => 'Usuario no autenticado.',
                ], 401);
            }

            // Validar los datos del request
            $validated = $request->validate([
                'pass1' => 'required|string',
                'pass2' => 'required|string|unique:admin,password',
            ]);

            // Verificar que pass1 coincida con la contraseña guardada
            if (!Hash::check($validated['pass1'], $admin->password)) {
                return response()->json([
                    'message' => 'La contraseña de verificación no coincide.',
                ], 403);
            }
            // Verificar si pass1 y pass2 son iguales (antes de hashearlas)
            if ($validated['pass1'] === $validated['pass2']) {
                return response()->json([
                    'message' => 'La nueva contraseña no puede ser igual a la actual.',
                ], 422);
            }
            $this->logout();
            // Actualizar la contraseña
            $admin->password = Hash::make($validated['pass2']);
            $admin->save();
            
            return response()->json([
                'message' => 'Contraseña actualizada exitosamente.',
            ], 200);
        } catch(\Exception $e){
            return response()->json([
                'message' => 'Error inesperado',
            ], 500);
        }
    }
    //borrar un registro de admin
    
    public function destroy(Request $request, $id)
    {
        // Buscar el administrador por ID
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json([
                'message' => 'Usuario no encontrado.',
            ], 404);
        }

        // Validar la contraseña proporcionada en el request
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        // Verificar que la contraseña proporcionada coincida con la contraseña almacenada
        if (!Hash::check($validated['password'], $admin->password)) {
            return response()->json([
                'message' => 'La contraseña de verificación no coincide.',
            ], 403);
        }

        if ($admin->id !== JWTAuth::user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para realizar esta acción.',
            ], 403);
        }

        $this->logout();
        // Eliminar el administrador
        $admin->delete();
        // Respuesta clara al cliente
        return response()->json([
            'message' => 'Cuenta eliminada exitosamente.',
            'status' => 200,
        ], 200);
    }

}