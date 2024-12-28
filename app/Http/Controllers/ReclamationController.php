<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $reclamation = Reclamation::all();
        return response()->json($reclamation);
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
        $reclamation = Reclamation::create([
            'user_id' => $request->user_id,
            'commande_id' => $request->commande_id,
            'genre_reclamation' => $request->genre_reclamation,
            'reclamation' => $request->clamation,
            'date_reclamation' => $request->date_reclamation,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $reclamation = Reclamation::find($id);
        return response()->json($reclamation);
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
        $reclamation = Reclamation::find($id);
        $reclamation->user_id = $request->user_id;
        $reclamation->commande_id = $request->commande_id;
        $reclamation->genre_reclamation = $request->genre_reclamation;
        $reclamation->reclamation = $request->reclamation;
        $reclamation->date_reclamation = $request->date_reclamation;
        $reclamation->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $reclamation = Reclamation::find($id);
        $reclamation->delete();
    }
}
