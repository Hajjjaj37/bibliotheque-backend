<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Paiment extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $paiment = Paiment::all();
        return response()->json($paiment);
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
        $paiment = Paiment::create([
            'user_id' => $request-> User,
            'produit_id' => $request-> produit,
            'commande_id' => $request-> commande,
            'methode' => $request-> methode,

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $paiment = Paiment::find($id);
        return response()->json($paiment);
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
        $paiment = Paiment::find($id);
        $paiment->user_id = $request->user;
        $paiment->produit_id = $request->produit;
        $paiment->commande_id = $request->commande;
        $paiment->methode = $request->methode;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $paiment = Paiment::find($id);
        $paiment->delete();
    }
}
