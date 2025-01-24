<?php


namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Authorization;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AuthorizationController extends Controller
{
    // consultar todas las autorizaciones
    public function index()
    {
        $authorizations = Authorization::all();

        return response()->json([
            'message' => 'Autorizaciones obtenidas exitosamente',
            'data' => $authorizations,
        ], 200);
    }
    /**
     * Crear un nuevo registro en la tabla authorization.
     */
    public function create(Request $request)
    {
        // Validar datos entrantes
        $validator = Validator::make($request->all(), [
            'no_doc' => 'required|unique:authorization,no_doc',
            'email' => 'required|email|unique:authorization,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error en la validación de los datos',
                'errors' => $validator->errors(),
            ], 400);
        }

        // Crear el registro
        $authorization = Authorization::create([
            'no_doc' => $request->input('no_doc'),
            'email' => $request->input('email'),
        ]);

        return response()->json([
            'message' => 'Registro creado exitosamente',
            'data' => $authorization,
        ], 201);
    }

    /**
     * Eliminar un registro de la tabla authorization por su ID.
     */
    public function delete($id)
    {
        $authorization = Authorization::find($id);

        if (!$authorization) {
            return response()->json([
                'message' => 'Registro no encontrado',
            ], 404);
        }

        $authorization->delete();

        return response()->json([
            'message' => 'Registro eliminado exitosamente',
        ], 200);
    }
    /**
     * Verifica si existe una coincidencia en la tabla 'authorization' 
     * para los campos 'no_doc' y 'email'.
     */
    public function checkAuthorization(Request $request)
    {
        $validatedData = $request->validate([
            'no_doc' => 'required|string',
            'email' => 'required|email',
        ]);

        // Buscar coincidencia en la tabla `authorization`
        $exists =  Authorization::
            where('no_doc', $validatedData['no_doc'])
            ->where('email', $validatedData['email'])
            ->exists();

        return response()->json(['found' => $exists]);
    }
}