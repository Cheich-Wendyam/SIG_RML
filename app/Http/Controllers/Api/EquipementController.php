<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipement;
use Illuminate\Support\Facades\Storage;

class EquipementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $equipements = Equipement::all();
        return response()->json(['content' => $equipements]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estdisponible' => 'boolean',
            'estmutualisable' => 'boolean',
            'etat' => 'string|in:neuf,bon état,usé,endommagé',
            'acquereur' => 'nullable|string',
            'typeacquisition' => 'required|string',
            'laboratoire_id' => 'required|exists:laboratoires,id'
        ]);

        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('equipements', 'public');
        }

        $equipement = Equipement::create($validatedData);

        return response()->json(['message' => 'Équipement ajouté avec succès', 'equipement' => $equipement], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $equipement = Equipement::find($id);

        if (!$equipement) {
            return response()->json(['message' => 'Équipement non trouvé'], 404);
        }

        return response()->json($equipement);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $equipement = Equipement::find($id);

        if (!$equipement) {
            return response()->json(['message' => 'Équipement non trouvé'], 404);
        }

        $validatedData = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'estdisponible' => 'boolean',
            'estmutualisable' => 'boolean',
            'etat' => 'string|in:neuf,bon état,usé,endommagé',
            'acquereur' => 'nullable|string',
            'typeacquisition' => 'sometimes|string',
            'laboratoire_id' => 'sometimes|exists:laboratoires,id'
        ]);

        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($equipement->image) {
                Storage::disk('public')->delete($equipement->image);
            }
            $validatedData['image'] = $request->file('image')->store('equipements', 'public');
        }

        $equipement->update($validatedData);

        return response()->json(['message' => 'Équipement mis à jour avec succès', 'equipement' => $equipement]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $equipement = Equipement::find($id);

        if (!$equipement) {
            return response()->json(['message' => 'Équipement non trouvé'], 404);
        }

        if ($equipement->image) {
            Storage::disk('public')->delete($equipement->image);
        }

        $equipement->delete();

        return response()->json(['message' => 'Équipement supprimé avec succès']);
    }

    /**
     * Afficher les réservations d'un équipement.
     */
    public function reservations($id)
    {
        $equipement = Equipement::findOrFail($id);
        return response()->json($equipement->reservations);
    }


}
