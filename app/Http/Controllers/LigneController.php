<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LigneController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $ligne = LigneCommande::all();
        return response()->json($ligne);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $ligne = LigneCommande::create([
            'commande_id' => $request->commande,
            'produit_id' => $request->produit,
            'quantite' => $request->quantite,
            'soustotal' => $request->soustotal,
        ]);

        return response()->json($ligne, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $ligne = LigneCommande::find($id);
        return response()->json($ligne);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $ligne = LigneCommande::find($id);
        $ligne->commande_id = $request->commande;
        $ligne->produit_id = $request->produit;
        $ligne->quantite = $request->quantite;
        $ligne->soustotal = $request->soustotal;
        $ligne->save();

        return response()->json($ligne);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $ligne = LigneCommande::find($id);
        $ligne->delete();

        
    }
}
