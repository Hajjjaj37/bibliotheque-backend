<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    //
    protected $fillable = [
        'user_id',
        'panier_id',
        'totaldachat',
        'nbrdachat',
        'nomproduit',
    ];
}
