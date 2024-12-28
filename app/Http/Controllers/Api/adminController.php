<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index(){
        $admins = Admin::all(); 
        
        if ($admins->isEmpty()) {
            $data = [
                'messaje'=>'no hay registros',
                'status'=> 404,
            ];
            return response()->json($data,404);
        }
        return response()->json($admins,200);
    }

    public function store(Request $request){
    // Validar los datos proporcionados
    $validator = Validator::make($request->all(), [
        'name' => 'required',
        'no_doc' => 'required',
        'email' => 'required|email|unique:admin',
        'username' => 'required|unique:admin',
        'password' => 'required|unique:admin',
        'main' => 'nullable|boolean' // Campo opcional
    ]);

    if ($validator->fails()) {
        $data = [
            'message' => 'Error en la validación de los datos',
            'errors' => $validator->errors(),
            'status' => 400
        ];
        return response()->json($data, 400);
    }

    // Verificar el requisito de "no_doc" en la tabla authorization si main es falso o no se proporciona
    if (!$request->boolean('main')) {
        $noDocExists = DB::table('authorization')->where('no_doc', $request->no_doc)->exists();
        $emailExists = DB::table('authorization')->where('email', $request->email)->exists();

        if (!$noDocExists) {
            return response()->json([
                'message' => 'El documento no está autorizado.',
                'status' => 403
            ], 403);
        }
        if (!$emailExists) {
            return response()->json([
                'message' => 'Dirección de correo no autorizado.',
                'status' => 403
            ], 403);
        }
    }

    // Crear el registro de admin
    $admin = Admin::create([
        'name' => $request->name,
        'no_doc' => $request->no_doc,
        'email' => $request->email,
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'main' => $request->boolean('main') // Convertir a booleano explícitamente
    ]);

    if (!$admin) {
        $data = [
            'message' => 'Error al crear el registro de administrador.',
            'status' => 500
        ];
        return response()->json($data, 500);
    }

    // Responder con éxito
    $data = [
        'admin' => $admin,
        'status' => 201
    ];
    return response()->json($data, 201);
    }

    public function show($id){
        $admin = Admin::find($id);
        if(!$admin){
            $data = [
                'messaje' => 'Datos no encontrados',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        $data = [
            'map' => $admin,
            'status' => 200
        ];
        return response()->json($data,200);
    }
    //borrar un registro
    public function destroy($id){
        $admin = Admin::find($id);
        if(!$admin){
            $data = [
                'messaje' => 'Datos no encontrados',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        $admin->delete();
        $data = [
            'map' => 'Datos eliminados',
            'status' => 200
        ];
        return response()->json($data,200);
    }
//actualizar un registro
    public function update(Request $request, $id){
        $admin = Admin::find($id);
        if(!$admin){
            $data = [
                'messaje' => 'Datos no encontrados',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        //validar con Validator
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'no_doc'=>'required',
            'email'=>'required|email',
            'username'=>'required',
            'password'=>'required|unique:admin',
            'main'=>'nullable|boolean'
        ]);
        if ($validator->fails()){
            $data = [
                'messaje' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data,400);
        }
        $admin->name = $request->name;
        $admin->no_doc = $request->no_doc;
        $admin->email = $request->email;
        $admin->username = $request->username;
        $admin->password = $request->password;
        $admin->password = Hash::make($request->input('password'));
        $admin->main = $request->boolean('main');
        $admin->save();
         
        $data = [
            'messaje' => 'Datos actualizados',
            'map' => $admin,
            'status' => 200
        ];
        return response()->json($data,200);
    }
    //actualizar un registro parcialmente
    public function updatePartial(Request $request, $id){
        $admin = Admin::find($id);
        if(!$admin){
            $data = [
                'messaje' => 'Datos no encontrados',
                'status' => 404
            ];
            return response()->json($data,404);
        }

        try {
            // Validar los datos entrantes
            $validatedData = $request->validate([
                'name' => 'string|nullable',
                'no_doc' => 'string|nullable',
                'email' => 'email|nullable',
                'username' => 'string|nullable',
                'password' => 'string|min:8|nullable', // Validar longitud de la contraseña
                'main' => 'nullable|boolean'
            ]);
        } catch (ValidationException $e) {
            // Responder con errores de validación
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'status' => 400
            ], 400);
        }
        
    
        // Actualizar solo los campos presentes
        if ($request->filled('name')) {
            $admin->name = $validatedData['name'];
        }
        if ($request->filled('no_doc')) {
            $admin->no_doc = $validatedData['no_doc'];
        }
        if ($request->filled('email')) {
            $admin->email = $validatedData['email'];
        }
        if ($request->filled('username')) {
            $admin->username = $validatedData['username'];
        }
        if ($request->filled('password')) {
            $admin->password = Hash::make($validatedData['password']); // Encriptar la contraseña
        }
        if ($request->filled('main')) {
            $admin->main = $validatedData['main']; // Encriptar la contraseña
        }
        
        $data = [
            'messaje' => 'Datos actualizados',
            'map' => $admin,
            'status' => 200
        ];
        return response()->json($data,200);
    }
}