<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
        //validar con Validator
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'no_doc'=>'required',
            'email'=>'required|email|unique:admin',
            'username'=>'required|unique:admin',
            'password'=>'required|unique:admin',
            'main'=>'nullable|boolean' // Campo opcional
        ]);
        if ($validator->fails()){
            $data = [
                'messaje' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 200
            ];
            return response()->json($data,400);
        }
        $admi = Admin::create([
            'name'=>$request->name,
            'no_doc'=>$request->no_doc,
            'email'=>$request->email,
            'username'=>$request->username,
            'password'=> Hash::make($request->input('password')), 
            'main' => $request->boolean('main')            
        ]);
        if (!$admi){
            $data = [
                'messaje' => 'Error en la carga de los datos',
                'status' => 500
            ];
            return response()->json($data,500);
        }
        $data = [
            'admi' => $admi,
            'status' => 201
        ];
        return response()->json($data,201);
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