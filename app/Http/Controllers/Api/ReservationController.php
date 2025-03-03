<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Auth;
use App\Mail\ReservationValidated;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Mail\ReservationRefused;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Laboratoire;


class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::all();
        return response()->json(['content' => $reservations]);
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
    try {
        // Validation des données
        $validatedData = $request->validate([
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'equipement_id' => 'required|exists:equipements,id',
            'motif' => 'nullable|string',
            'commentaire' => 'nullable|string',
            'info_utilisateur' => 'nullable|json',
        ]);

        // Vérifier si un utilisateur est connecté
        $user = Auth::user();
        $info_utilisateur = null;
        $user_id = null;

        if ($user) {
            // Si connecté, `user_id` est obligatoire et `info_utilisateur` est rempli automatiquement
            $user_id = $user->id;
            $info_utilisateur = json_encode([
                'firstname' => $user->firstname ?? '',
                'lastname' => $user->lastname ?? '',
                'email' => $user->email ?? '',
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
            ]);
        } else {
            // Si pas connecté, `info_utilisateur` devient obligatoire
            $request->validate([
                'info_utilisateur' => 'required|json',
            ]);
            $info_utilisateur = $validatedData['info_utilisateur'];
        }

        // Générer un code unique pour la réservation
        $code = 'RES-' . strtoupper(bin2hex(random_bytes(3))) . '-' . uniqid();
        // Création de la réservation
        $reservation = Reservation::create([
            'date_debut' => $validatedData['date_debut'],
            'date_fin' => $validatedData['date_fin'],
            'equipement_id' => $validatedData['equipement_id'],
            'motif' => $validatedData['motif'] ?? null,
            'commentaire' => $validatedData['commentaire'] ?? null,
            'user_id' => $user_id,
            'info_utilisateur' => $info_utilisateur,
            'code' => $code
        ]);



        return response()->json([
            'message' => 'Réservation effectuée avec succès',
            'reservation' => $reservation
        ], 201);

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Retourner les erreurs sous format JSON
        return response()->json([
            'message' => 'Erreur de validation',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        // Gérer toute autre erreur (ex: problème serveur, base de données)
        return response()->json([
            'message' => 'Une erreur est survenue',
            'error' => $e->getMessage()
        ], 500);
    }
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
    public function update(Request $request, $id)
    {
        try {
            // Vérifier si la réservation existe
            $reservation = Reservation::findOrFail($id);

            // Validation des données entrantes
            $validatedData = $request->validate([
                'date_debut' => 'sometimes|date',
                'date_fin' => 'sometimes|date|after_or_equal:date_debut',
                'motif' => 'nullable|string',
                'commentaire' => 'nullable|string',
                'info_utilisateur' => 'nullable|json',
            ]);

            // Mise à jour des champs
            $reservation->update($validatedData);

            return response()->json([
                'message' => 'Réservation mise à jour avec succès',
                'reservation' => $reservation
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $reservation = Reservation::findOrFail($id);
            $reservation->delete();
            return response()->json([
                'message' => 'Réservation supprimée avec succès'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Une erreur est survenue',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function validerReservation($id)
{
    // Récupérer la réservation par ID
    $reservation = Reservation::findOrFail($id);



    // Changer le statut en "validée"
    $reservation->status = 'acceptee';
    $reservation->save();

    // Récupérer les informations de l'utilisateur
    $userEmail = null;

    // Si un utilisateur est connecté, utiliser son e-mail
    if ($reservation->user_id) {
        $user = User::find($reservation->user_id);
        $userEmail = $user ? $user->email : null;
    } else {
        // Si pas d'utilisateur connecté, extraire l'e-mail de info_utilisateur
        $infoUtilisateur = json_decode($reservation->info_utilisateur, true);
        $userEmail = $infoUtilisateur['email'] ?? null;
    }

    // Envoyer un e-mail de validation à l'utilisateur
    if ($userEmail) {
        Mail::to($userEmail)->send(new ReservationValidated($reservation));
    }

    return response()->json(['message' => 'Réservation validée avec succès.', 'reservation' => $reservation], 200);
}

// annuler une réservation
public function annulerReservation($code) {
    // Récupérer la réservation par code
    $reservation = Reservation::where('code', $code)->first();

    if (!$reservation) {
        return response()->json(['message' => 'Réservation non trouvée'], 404);
    }

    // Changer le statut en "annulée"
    $reservation->status = 'annulee';
    $reservation->save();

    return response()->json(['message' => 'Réservation annulée avec succès.', 'reservation' => $reservation], 200);
}

// get reservation by code
public function getReservationByCode(string $code) {
    try {
        // Récupérer la réservation avec l'equipment en fonction du code

        $reservation = Reservation::where('code', $code)->with('equipement')->first();

        if (!$reservation) {
            return response()->json(['message' => 'Réservation non trouvée'], 404);
        }

        return response()->json(['reservation' => $reservation]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Une erreur est survenue lors de la récupération de la réservation.',
            'error' => $e->getMessage()
        ], 500);
    }
}



// rejeter une réservation avec id et envoi d'email
public function rejeterReservation($id) {
    // Récupérer la réservation par ID
    $reservation = Reservation::findOrFail($id);

    // Changer le statut en "refusée"
    $reservation->status = 'refusee';
    $reservation->save();

    // Récupérer les informations de l'utilisateur
    $userEmail = null;

    // Si un utilisateur est connecté, utiliser son e-mail
    if ($reservation->user_id) {
        $user = User::find($reservation->user_id);
        $userEmail = $user ? $user->email : null;
    } else {
        // Si pas d'utilisateur connecté, extraire l'e-mail de info_utilisateur
        $infoUtilisateur = json_decode($reservation->info_utilisateur, true);
        $userEmail = $infoUtilisateur['email'] ?? null;
    }

    // Envoyer un e-mail de validation à l'utilisateur
    if ($userEmail) {
        Mail::to($userEmail)->send(new ReservationRefused($reservation));
    }

    return response()->json(['message' => 'Réservation refusée avec succès.', 'reservation' => $reservation], 200);
}

// get user reservations
public function getUserReservations() {
    try {
        $user = Auth::user();
        $reservations = $user->reservations()->get();
        return response()->json(['reservations' => $reservations]);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Une erreur est survenue', 'error' => $e->getMessage()], 500);
    }

}

// obtenir les reservations des equipements d'un laboratoire
public function getReservationsByLaboratoire($id)
{
    $laboratoire = Laboratoire::with('equipements.reservations')->find($id);

    if (!$laboratoire) {
        return response()->json(['message' => 'Laboratoire non trouvé'], 404);
    }

    // Récupérer toutes les réservations des équipements du laboratoire
    $reservations = $laboratoire->equipements->flatMap->reservations;

    return response()->json($reservations);
}


}
