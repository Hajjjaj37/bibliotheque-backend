<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class PaginationController extends Controller
{
    public function paginateProduct(){
        $product = DB::table('users')->paginate(3);

        return response()->json([
            "products" => $product
        ]);
    }
}
