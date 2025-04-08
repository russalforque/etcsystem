<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::with(['currentContract.room'])->get();
        return response()->json($tenants);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|unique:tenants',
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenant = Tenant::create($request->all());
        return response()->json($tenant, 201);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load([
            'currentContract.room',
            'maintenanceRequests' => function($query) {
                $query->latest()->take(5);
            }
        ]);
        return response()->json($tenant);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'id_number' => 'required|string|unique:tenants,id_number,' . $tenant->id,
            'date_of_birth' => 'required|date',
            'address' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $tenant->update($request->all());
        return response()->json($tenant);
    }

    public function destroy(Tenant $tenant)
    {
        if ($tenant->currentContract) {
            return response()->json([
                'message' => 'Cannot delete tenant with active contract'
            ], 422);
        }

        $tenant->delete();
        return response()->json(null, 204);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $tenants = Tenant::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->with(['currentContract.room'])
            ->get();

        return response()->json($tenants);
    }
} 