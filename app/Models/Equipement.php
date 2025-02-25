<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Equipement extends Model
{
    protected $table = 'equipements';
    protected $fillable = ['nom', 'description', 'image',
                            'estdisponible',
                            'estmutualisable', 'etat',
                            'acquereur', 'typeacquisition', 'laboratoire_id'];

    public function laboratoire()
    {
        return $this->belongsTo(Laboratoire::class);
    }

}
