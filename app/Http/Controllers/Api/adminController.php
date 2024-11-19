<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use function PHPSTORM_META\map;

class adminController extends Controller
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
            'password'=>'required|unique:admin'
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
            'password'=> Hash::make($request->input('password')), // Encripta la contraseña
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




}