<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    //
    protected $fillable = ['user_id', 'commande_id','status', 'la_date_livraison'];
}
