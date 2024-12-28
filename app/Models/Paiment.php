<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paiment extends Model
{
    //
    protected $fillable = ['user_id', 'produit_id', 'commande_id','methode' ];
}
