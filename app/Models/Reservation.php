<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = 'reservations';

    protected $fillable = [
        'datedebut',
        'datefin',
        'code',
        'equipement_id',
        'user_id',
        'status',
        'motif',
        'commentaire',
        'info_utilisateur'
    ];

    public function equipement()
    {
        return $this->belongsTo(Equipement::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
