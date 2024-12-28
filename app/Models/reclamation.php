<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reclamation extends Model
{
    //
    protected $fillable = ['user_id', 'commande_id', 'genre_reclamation', 'reclamation','date_reclamation'];
}
