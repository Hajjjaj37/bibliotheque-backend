<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;

class ReclamationController extends Controller
{
    public function index()
    {
        $reclamations = Reclamation::all();
        return response()->json([
            'status' => 'success',
            'data' => $reclamations
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'commande_id' => 'required|exists:commandes,id',
            'genre_reclamation' => 'required|string',
            'reclamation' => 'required|string',
            'date_reclamation' => 'required|date'
        ]);

        $reclamation = Reclamation::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Reclamation created successfully',
            'data' => $reclamation
        ], 201);
    }

    public function show($id)
    {
        $reclamation = Reclamation::find($id);
        
        if (!$reclamation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reclamation not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $reclamation
        ]);
    }

    public function update(Request $request, $id)
    {
        $reclamation = Reclamation::find($id);

        if (!$reclamation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reclamation not found'
            ], 404);
        }

        $request->validate([
            'user_id' => 'exists:users,id',
            'commande_id' => 'exists:commandes,id',
            'genre_reclamation' => 'string',
            'reclamation' => 'string',
            'date_reclamation' => 'date'
        ]);

        $reclamation->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Reclamation updated successfully',
            'data' => $reclamation
        ]);
    }

    public function destroy($id)
    {
        $reclamation = Reclamation::find($id);

        if (!$reclamation) {
            return response()->json([
                'status' => 'error',
                'message' => 'Reclamation not found'
            ], 404);
        }

        $reclamation->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Reclamation deleted successfully'
        ]);
    }
} 