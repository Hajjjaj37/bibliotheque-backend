<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $panier = Panier::all();
        return response()->json($panier);
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
        $request = Panier::create([
            'user_id' => $request->User,
            'categorie_id' => $request->categorie,

        ]);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $panier = Panier::find($id);
        return response()->json($panier);
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
        $panier = Panier::find($id);
        $panier->user_id = $request->User;
        $panier->categorie_id = $request->categorie;
        $panier->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $panier = Panier::find($id);
        $panier->delete();
    }
}
