<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with(['room', 'tenant'])
            ->latest()
            ->get();
        return response()->json($contracts);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:tenants,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'terms_and_conditions' => 'required|string',
            'additional_terms' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $room = Room::findOrFail($request->room_id);
            if ($room->status !== 'available') {
                return response()->json([
                    'message' => 'Room is not available for contract'
                ], 422);
            }

            $contract = Contract::create($request->all());
            $room->update(['status' => 'occupied']);

            DB::commit();
            return response()->json($contract, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating contract',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Contract $contract)
    {
        $contract->load(['room', 'tenant', 'payments']);
        return response()->json($contract);
    }

    public function update(Request $request, Contract $contract)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'monthly_rent' => 'required|numeric|min:0',
            'security_deposit' => 'required|numeric|min:0',
            'status' => 'required|in:active,expired,terminated',
            'terms_and_conditions' => 'required|string',
            'additional_terms' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            if ($request->status === 'expired' || $request->status === 'terminated') {
                $contract->room->update(['status' => 'available']);
            }

            $contract->update($request->all());

            DB::commit();
            return response()->json($contract);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating contract',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Contract $contract)
    {
        if ($contract->status === 'active') {
            return response()->json([
                'message' => 'Cannot delete active contract'
            ], 422);
        }

        $contract->delete();
        return response()->json(null, 204);
    }

    public function upcomingExpirations()
    {
        $contracts = Contract::with(['room', 'tenant'])
            ->where('status', 'active')
            ->where('end_date', '<=', now()->addDays(30))
            ->orderBy('end_date')
            ->get();

        return response()->json($contracts);
    }

    public function terminate(Contract $contract)
    {
        if ($contract->status !== 'active') {
            return response()->json([
                'message' => 'Contract is not active'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contract->update([
                'status' => 'terminated',
                'end_date' => now()
            ]);

            $contract->room->update(['status' => 'available']);

            DB::commit();
            return response()->json($contract);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error terminating contract',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 