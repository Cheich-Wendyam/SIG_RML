<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Laboratoire;

class LaboratoireController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $laboratoires = Laboratoire::all();
        return response()->json(['content' => $laboratoires]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsable_id' => 'required|exists:users,id',
            'ufr_id' => 'required|exists:ufrs,id',
        ]);

        $laboratoire = Laboratoire::create($request->all());

        return response()->json($laboratoire, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $laboratoire = Laboratoire::findOrFail($id);
        return response()->json($laboratoire);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'responsable_id' => 'required|exists:users,id',
            'ufr_id' => 'required|exists:ufrs,id',
        ]);

        $laboratoire = Laboratoire::findOrFail($id);
        $laboratoire->update($request->all());

        return response()->json($laboratoire);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laboratoire = Laboratoire::findOrFail($id);
        $laboratoire->delete();

        return response()->json(null, 204);
    }

    public function getEquipements($id)
    {
        $laboratoire = Laboratoire::findOrFail($id);
        $equipements = $laboratoire->equipements;
        return response()->json($equipements);
    }

    public function getLaboratoireByResponsable($id)
    {
        $laboratoire = Laboratoire::where('responsable_id', $id)->get();
        return response()->json($laboratoire);
    }
}
