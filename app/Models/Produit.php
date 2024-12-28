<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    //
    protected $fillable = [
        "name",
        "description",
        "categorie",
        "prix",
        "quantité",
        "image",
    ];
}
