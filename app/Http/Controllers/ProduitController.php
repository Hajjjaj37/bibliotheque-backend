<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produit;
use Illuminate\Support\Facades\DB;

class ProduitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // $produit = Produit::all();

        $produit = DB::select('SELECT * FROM produits');

        return response()->json($produit);
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
        if($request->has("image")){

            $file = $request->image;
            $fileName = time(). "_". $file->getClientOriginalName();
            $file->move("produit", $fileName);
        }

        //
        $request = Produit::create([
            'name' => $request->name,
            'description' => $request->description,
            'categorie' => $request->categorie,
            'prix' => $request->prix,
            'quantité' => $request->quantité,
            'image' => $fileName,
        ]);
        return response()->json([
            "message"=> "produit added successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $produit = Produit::find($id);
        return response()->json($produit);
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
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'categorie' => 'required|string|max:100',
            'prix' => 'required|min:0',
            'quantité' => 'nullable',
            'image' => 'nullable|string',
        ]);

        if (!empty($request['image'])) {

            if($request->has("image")){

                $file = $request->image;
                $fileName = time(). "_". $file->getClientOriginalName();
                $file->move("produit", $fileName);
            }

            $produit = Produit::find($id);
            $validated["name"] = $produit->name;
            $validated["description"] = $produit->description;
            $validated["categorie"] = $produit->categorie;
            $validated["prix"] = $produit->prix;
            $validated["quantité"] = $produit->quantité;
            $validated["image"] = $fileName;

        }else{

            $produit = Produit::find($id);
            $validated["name"] = $produit->name;
            $validated["description"] = $produit->description;
            $validated["categorie"] = $produit->categorie;
            $validated["prix"] = $produit->prix;
            $validated["quantité"] = $produit->quantité;
            $validated["image"] = $produit->image;
        }

        $produit->update($validated);

        return response()->json([
            'message' => 'Livre mis à jour avec succès!',
            'livre' => $produit,
        ], 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $produit = Produit::find($id);
        $produit->delete();
    }
}

