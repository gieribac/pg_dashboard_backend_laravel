<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Authorization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    //consultar administradores
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

    //registrar administrador
    public function store(Request $request)
    {
        try {
            // Validar los datos proporcionados (sin incluir 'main')
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'no_doc' => 'required|unique:admin,no_doc',
                'email' => 'required|email|unique:admin,email',
                'username' => 'required|unique:admin,username',
                'password' => 'required|string|unique:admin,password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Error en la validación de los datos',
                    'errors' => $validator->errors(),
                    'status' => 400
                ], 400);
            }

            // Verificar si no hay registros en la tabla 'admin'
            if (!Admin::exists()) {
                // Si no hay administradores, el primero será 'main' = true
                $admin = Admin::create([
                    'name' => $request->name,
                    'no_doc' => $request->no_doc,
                    'email' => $request->email,
                    'username' => $request->username,
                    'password' => Hash::make($request->password),
                    'main' => true
                ]);

                return response()->json([
                    'admin' => $admin,
                    'status' => 201
                ], 201);
            }

            // Obtener el valor de 'main' desde la tabla 'authorization'
            $authorization = Authorization::where('no_doc', $request->no_doc)
                ->where('email', $request->email)
                ->first();

            if (!$authorization) {
                return response()->json([
                    'message' => 'El documento o email no están autorizados.',
                    'status' => 403
                ], 403);
            }

            // Convertir el valor de 'main' a booleano
            $main = (bool) ($authorization->main ?? false);

            // Crear el nuevo registro admin
            $admin = Admin::create([
                'name' => $request->name,
                'no_doc' => $request->no_doc,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'main' => $main
            ]);

            return response()->json([
                'admin' => $admin,
                'status' => 201
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al procesar la solicitud.',
                'error' => $e->getMessage(),
                'status' => 500
            ], 500);
        }
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
            'no_doc'=>'required|unique:admin,no_doc',
            'email'=>'required|email|unique:admin,email,',
            'username'=>'required|unique:admin,username',
            'password'=>'required|unique:admin,password',
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
                'no_doc' => 'string|nullable|unique:admin,no_doc',
                'email' => 'email|nullable|unique:admin,email',
                'username' => 'string|nullable|unique:admin,username',
                'password' => 'string|min:8|nullable,unique:admin,password', // Validar longitud de la contraseña
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
        $admin->save();
        return response()->json($data,200);
    }
}