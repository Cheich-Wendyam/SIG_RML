<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ufr extends Model
{
    protected $table = 'ufrs';

    protected $fillable = ['intitule', 'description'];

    public function laboratoires()
    {
        return $this->hasMany(Laboratoire::class);
    }
}
