<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Afficher tous les utilisateurs.
     */
    public function index()
    {
        $users = User::all();
        return response()->json(['content' => $users]);
    }

    /**
     * Ajouter un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['reservant', 'responsable', 'admin'])],
        ]);

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'Utilisateur ajouté avec succès', 'user' => $user], 201);
    }

    /**
     * Afficher un utilisateur spécifique.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    /**
     * Modifier un utilisateur.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['sometimes', 'string', Rule::unique('users', 'phone')->ignore($user->id)],
            'role' => ['sometimes', Rule::in(['reservant', 'responsable', 'admin'])],
            'password' => 'sometimes|string|min:6',
        ]);

        $data = $request->only(['firstname', 'lastname', 'email', 'phone', 'role']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès', 'user' => $user]);
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé avec succès']);
    }

    /**
     * Afficher les laboratoires d'un utilisateur.
     */
    public function laboratoires(string $id)
    {
        $user = User::findOrFail($id);
        return response()->json($user->laboratoires);
    }

     // get users by role
     public function getUsersbyRole(string $role)
{
    // Vérifier si le rôle est valide (ajuste selon tes rôles disponibles)
    $rolesDisponibles = ['admin', 'reservant', 'responsable'];

    if (!in_array($role, $rolesDisponibles)) {
        return response()->json([
            'message' => 'Rôle invalide. Rôles disponibles : ' . implode(', ', $rolesDisponibles)
        ], 400);
    }

    // Récupérer les utilisateurs du rôle donné
    $users = User::where('role', $role)->get();

    if ($users->isEmpty()) {
        return response()->json([
            'message' => 'Aucun utilisateur trouvé pour ce rôle.'
        ], 404);
    }

    return response()->json($users, 200);
}



}

