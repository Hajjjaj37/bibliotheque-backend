<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommandeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $commande = Produit::all();
        return response()->json($commande);
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
        $request = Commande::create([
            'user_id' => $request->User,
            'panier_id' => $request->Panier,
            'totaldachat' => $request->totaldachat,
            'nbrdachat' => $request->nbrdachat,
            'nomproduit' => $request->nomproduit,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $commande = Commande::find($id);
        return response()->json($commande);
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
        $commande = Commande::find($id);
        $commande->user_id = $request->User;
        $commande->panier_id = $request->Panier;
        $commande->totaldachat = $request->totaldachat;
        $commande->nbrdachat = $request->nbrdachat;
        $commande->nomproduit = $request->nomproduit;
        $produit->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $commande = Commande::find($id);
        $commande->delete();
    }
}
