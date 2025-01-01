<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class reclamation extends Model
{
    protected $fillable = [
        'user_id', 
        'commande_id', 
        'genre_reclamation', 
        'reclamation', 
        'date_reclamation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'commande_id');
    }
}
