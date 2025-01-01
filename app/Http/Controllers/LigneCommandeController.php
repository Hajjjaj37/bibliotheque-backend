<?php

namespace App\Http\Controllers;

use App\Models\LigneCommande;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class LigneCommandeController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $ligneCommandes = LigneCommande::paginate(10);

            return response()->json([
                'status' => 'success',
                'data' => $ligneCommandes
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch ligne commandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'commande_id' => 'required|exists:commandes,id',
                'produit_id' => 'required|exists:produits,id',
                'quantite' => 'required|integer|min:1',
                'soustotal' => 'required|numeric|min:0',
                'etat' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ligneCommande = LigneCommande::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Ligne commande created successfully',
                'data' => $ligneCommande
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create ligne commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $ligneCommande = LigneCommande::find($id);

            if (!$ligneCommande) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ligne commande not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $ligneCommande
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch ligne commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $ligneCommande = LigneCommande::find($id);

            if (!$ligneCommande) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ligne commande not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'commande_id' => 'exists:commandes,id',
                'produit_id' => 'exists:produits,id',
                'quantite' => 'integer|min:1',
                'soustotal' => 'numeric|min:0',
                'etat' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $ligneCommande->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Ligne commande updated successfully',
                'data' => $ligneCommande
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update ligne commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $ligneCommande = LigneCommande::find($id);

            if (!$ligneCommande) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ligne commande not found'
                ], 404);
            }

            $ligneCommande->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Ligne commande deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete ligne commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
