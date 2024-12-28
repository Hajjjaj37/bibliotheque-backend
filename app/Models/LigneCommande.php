<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LigneCommande extends Model
{
    //
    protected $fillable = ['commande_id', 'produit_id', 'quantite', 'soustotal'];
}
