<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratoire extends Model
{
    protected $table = 'laboratoires';

    protected $fillable = [
        'nom',
        'description',
        'responsable_id',
        'ufr_id',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function ufr()
    {
        return $this->belongsTo(Ufr::class);
    }

    public function equipements()
    {
        return $this->hasMany(Equipement::class);
    }
}
