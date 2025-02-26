<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ufr;


class UfrController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ufrs = Ufr::all();
        return response()->json(['content' => $ufrs]);
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

        $validatedData = $request->validate([
            'intitule' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Créer une nouvelle Ufr avec les données validées
        $ufr = Ufr::create($validatedData);

        return response()->json($ufr, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

}
