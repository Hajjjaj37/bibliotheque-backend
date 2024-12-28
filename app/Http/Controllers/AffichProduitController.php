<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffichProduitController extends Controller
{
    public function jibliyacounterdyalproduit(){
        $produit = DB::select('SELECT * FROM produits');
        return response()->json([
            "hajjaj"=> $produit,
            "message"=> "salam"
        ]);
    }
}
