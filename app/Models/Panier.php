<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    //
    protected $fillable = [
        'user_id',
        'categorie_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
