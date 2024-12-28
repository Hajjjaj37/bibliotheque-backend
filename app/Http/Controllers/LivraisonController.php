<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LivraisonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $livraison = Livraison::all();
        return response()->json($livraison);
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
        $livraison = Livraison::create([
            'user_id' => $request->user,
            'commande_id' => $request->commande,
            'status' => $request->status,
            'la_date_livraison' => $request->la_date_livraison,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $livraison = Livraison::find($id);
        return response()->json($livraison);
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
        $livraison = Livraison::find($id);
        $livraison->user_id = $request->user;
        $livraison->commande_id = $request->commande;
        $livraison->status = $request->status;
        $livraison->la_date_livraison = $request->la_date_livraison;
        $livraison->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $livraison = Livraison::find($id);
        $livraison->delete();
    }
}
