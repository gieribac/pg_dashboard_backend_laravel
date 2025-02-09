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
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        $maps = $this->getMapsByRequest($filter);

        if ($maps === null) {
            return response()->json([
                'message' => 'Formato de petición no válido',
                'status' => 400,
            ], 400);
        }

        if ($maps->isEmpty()) {
            return response()->json([
                'message' => 'No hay mapas registrados',
                'status' => 404,
            ], 404);
        }

        return response()->json($maps, 200);
    }

    /**
     * Obtiene los mapas según el contenido de filter
     */
    private function getMapsByRequest(?string $filter)
    {
        return match ($filter) {
            null, '' => Map::all(), 
            'public' => Map::where('post', true)->get(),
            default  => null 
        };
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

