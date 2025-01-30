<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Map;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class MapController extends Controller
{
    //query bd
    public function index(){
        //llamar el modelo Maps y retornar all() si el arreglo esta vacio
        $maps = Map::all(); 
        
        //condicionar si el arreglo está vacío
        if ($maps->isEmpty()) {
            $data = [
                'messaje'=>'no hay mapas registrados',
                'status'=> 404,
            ];
            return response()->json($data,404);
        }
        // Transformar los datos para convertir 0/1 en false/true
        // $maps = $maps->map(function ($map) {
        //     // Ajusta las columnas booleanas según sea necesario
        //     $map->post = (bool) $map->post; 
        //     return $map;
        // });

        // Responder con el arreglo transformado y el código 200
        return response()->json($maps, 200);
    }

    //write bd
    public function store(Request $request){
        //validar con Validator
        $validator = Validator::make($request->all(),[
            'post'=>'required',
            'title'=>'required',
            'description'=>'required',
            'author'=>'required',
            'urlDashboard'=>'required|unique:map',
            'place'=>'required'
        ]);
        if ($validator->fails()){
            $data = [
                'messaje' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 200
            ];
            return response()->json($data,400);
        }
        $map = Map::create([
            'post'=>$request->post,
            'title'=>$request->title,
            'description'=>$request->description,
            'author'=>$request->author,
            'urlDashboard'=>$request->urlDashboard,
            'place'=>$request->place,
        ]);
        if (!$map){
            $data = [
                'messaje' => 'Error en la carga de un dashboard',
                'status' => 500
            ];
            return response()->json($data,500);
        }
        $data = [
            'map' => $map,
            'status' => 201
        ];
        return response()->json($data,201);
    }
    //consultar por id
    public function show($id){
        $map = Map::find($id);
        if(!$map){
            $data = [
                'messaje' => 'Dashboard no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        $data = [
            'map' => $map,
            'status' => 200
        ];
        return response()->json($data,200);
    }
    //borrar un registro
    public function destroy($id){
        $map = Map::find($id);
        if(!$map){
            $data = [
                'messaje' => 'Dashboard no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        $map->delete();
        $data = [
            'map' => 'Dashboard eliminado',
            'status' => 200
        ];
        return response()->json($data,200);
    }
    //actualizar un registro
    public function update(Request $request, $id){
        $map = Map::find($id);
        if(!$map){
            $data = [
                'messaje' => 'Dashboard no encontrado',
                'status' => 404
            ];
            return response()->json($data,404);
        }
        //validar con Validator
        $validator = Validator::make($request->all(),[
            'post'=>'required',
            'title'=>'required',
            'description'=>'required',
            'author'=>'required',
            'urlDashboard'=>'required|unique:map',
            'place'=>'required',
        ]);
        if ($validator->fails()){
            $data = [
                'messaje' => 'Error en la validacion de los datos',
                'errors' => $validator->errors(),
                'status' => 400
            ];
            return response()->json($data,400);
        }
        $map->post = $request->post;
        $map->title = $request->title;
        $map->description =$request->description;
        $map->author = $request->author;
        $map->urlDashboard = $request->urlDashboard;
        $map->place = $request->place;
        $map->save();
        
        $data = [
            'messaje' => 'Dashboard actualizado',
            'map' => $map,
            'status' => 200
        ];
        return response()->json($data,200);
    }
    //actualizar un registro parcialmente
    public function updatePartial(Request $request, $id)
    {
        $map = Map::find($id);
        if (!$map) {
            $data = [
                'message' => 'Dashboard no encontrado',
                'status' => 404
            ];
            return response()->json($data, 404);
        }
    
        try {
            $validatedData = $request->validate([
                'post' => 'nullable|boolean',
                'title' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'author' => 'nullable|string|max:255',
                'urlDashboard' => 'nullable|string',
                'place' => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors(),
                'status' => 400
            ], 400);
        }
    
        // Actualizar solo los campos presentes
        if ($request->filled('post')) {
            $map->post = $validatedData['post'];
        }
        if ($request->filled('title')) {
            $map->title = $validatedData['title'];
        }
        if ($request->filled('description')) {
            $map->description = $validatedData['description'];
        }
        if ($request->filled('author')) {
            $map->author = $validatedData['author'];
        }
        if ($request->filled('urlDashboard')) {
            $map->urlDashboard = $validatedData['urlDashboard'];
        }
        if ($request->filled('place')) {
            $map->place = $validatedData['place'];
        }
    
        // Guardar los cambios en la base de datos
        $map->save();
    
        $data = [
            'message' => 'Dashboard actualizado',
            'map' => $map,
            'status' => 200
        ];
        return response()->json($data, 200);
    }
    
}

