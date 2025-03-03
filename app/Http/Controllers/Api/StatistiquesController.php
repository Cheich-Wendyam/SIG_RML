<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Equipement;
use App\Models\User;
use App\Models\Laboratoire;
use App\Models\Ufr;
use Carbon\Carbon;

class StatistiquesController extends Controller
{
    public function index()
    {
        // Nombre total de réservations
        $totalReservations = Reservation::count();

        // Nombre total d'équipements
        $totalEquipements = Equipement::count();

        // Nombre total de laboratoires
        $totalLaboratoires = Laboratoire::count();

        // Nombre total d'UFR
        $totalUfrs = Ufr::count();

        // Nombre total d'utilisateurs
        $totalUsers = User::count();

        // le nombre de réservations en attente
        $reservationsEnAttente = Reservation::where('status', 'en attente')->count();

        // Nombre de réservations du mois actuel
        $reservationsCeMois = Reservation::whereMonth('created_at', Carbon::now()->month)->count();

        //  Prévision des réservations pour le mois prochain en utilisant une tendance linéaire
        $reservationsLastThreeMonths = Reservation::whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $prochainesReservations = $this->linearTrendPrediction($reservationsLastThreeMonths);

        //  Équipements les plus utilisés (Top 5)
        $equipementsUtilises = Reservation::select('equipement_id', DB::raw('count(*) as total_reservations'))
            ->with(['equipement' => function($query) {
                $query->select('id', 'nom', 'laboratoire_id')->with('laboratoire:id,nom');
            }])
            ->groupBy('equipement_id')
            ->orderByDesc('total_reservations')
            ->take(5)
            ->get();

        $totalReservationsNonZero = max($totalReservations, 1); // Éviter la division par zéro
        $equipementsUtilisesPercentage = ($equipementsUtilises->avg('total_reservations') / $totalReservationsNonZero) * 100;

        //  Équipements susceptibles d’avoir des pannes (Top 5 par fréquence d'utilisation)
        $equipementsPannes = DB::table('equipements')
            ->select('equipements.id', 'equipements.nom', DB::raw('count(reservations.id) as total_utilisation'))
            ->leftJoin('reservations', 'equipements.id', '=', 'reservations.equipement_id')
            ->groupBy('equipements.id', 'equipements.nom')
            ->orderByDesc('total_utilisation')
            ->take(5)
            ->get();

        $equipementsPannesPercentage = ($equipementsPannes->avg('total_utilisation') / $totalReservationsNonZero) * 100;

        //  Statistiques des utilisateurs actifs cette semaine et ce mois
        $usersThisWeek = User::where('last_login', '>=', Carbon::now()->startOfWeek())->count();
        $usersThisMonth = User::where('last_login', '>=', Carbon::now()->startOfMonth())->count();

        //  Récupérer les données des trois derniers mois
        $usersLastThreeMonths = User::whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
            ->select(DB::raw('MONTH(created_at) as month'), DB::raw('YEAR(created_at) as year'), DB::raw('count(*) as total'))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        //  Calculer la prévision des nouveaux utilisateurs pour le mois prochain
        $usersNextMonth = $this->linearTrendPrediction($usersLastThreeMonths);

        //  Calcul du pourcentage des nouveaux utilisateurs prévus pour le mois prochain
        $usersNextMonthPercentage = ($usersNextMonth / max($totalUsers, 1)) * 100;


        // Retourner les statistiques en JSON
        return response()->json([
            'total_reservations' => $totalReservations,
            'total_equipements' => $totalEquipements,
            'total_users' => $totalUsers,
            'reservations_ce_mois' => $reservationsCeMois,
            'prochaines_reservations' => round($prochainesReservations),
            'equipements_utilises_percentage' => round($equipementsUtilisesPercentage, 2),
            'equipements_susceptibles_pannes_percentage' => round($equipementsPannesPercentage, 2),
            'users_this_week' => $usersThisWeek,
            'users_this_month' => $usersThisMonth,
            'users_next_month' => round($usersNextMonth),
            'users_next_month_percentage' => round($usersNextMonthPercentage, 2),
            'total_laboratoires' => $totalLaboratoires,
            'total_ufrs' => $totalUfrs,
            'reservations_en_attente' => $reservationsEnAttente,
            'equipements_utilises' => $equipementsUtilises,
            'equipements_pannes' => $equipementsPannes
        ]);
    }

    /**
     * Prédit la valeur du mois suivant en utilisant une régression linéaire simple
     */
    private function linearTrendPrediction($data)
    {
        if ($data->count() < 2) {
            return $data->avg('total') ?? 0; // Si pas assez de données, utiliser la moyenne
        }

        $x = []; // Mois (1, 2, 3...)
        $y = []; // Valeurs (total des réservations/utilisateurs)

        foreach ($data as $index => $row) {
            $x[] = $index + 1;
            $y[] = $row->total;
        }

        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = 0;
        $sumXX = 0;

        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $y[$i];
            $sumXX += $x[$i] * $x[$i];
        }

        // Calcul des coefficients de la droite de régression y = ax + b
        $a = ($n * $sumXY - $sumX * $sumY) / max(($n * $sumXX - $sumX * $sumX), 1);
        $b = ($sumY - $a * $sumX) / $n;

        // Prédiction pour x = n+1 (mois suivant)
        $prediction = $a * ($n + 1) + $b;

        return max($prediction, 0); // Éviter les valeurs négatives
    }
}
