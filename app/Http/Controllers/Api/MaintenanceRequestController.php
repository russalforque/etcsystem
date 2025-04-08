<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MaintenanceRequestController extends Controller
{
    public function index()
    {
        $requests = MaintenanceRequest::with(['room', 'tenant'])
            ->latest()
            ->get();
        return response()->json($requests);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'tenant_id' => 'required|exists:tenants,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'requested_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $room = Room::findOrFail($request->room_id);
            $maintenanceRequest = MaintenanceRequest::create($request->all());

            if ($request->priority === 'urgent') {
                $room->update(['status' => 'maintenance']);
            }

            DB::commit();
            return response()->json($maintenanceRequest, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating maintenance request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(MaintenanceRequest $maintenanceRequest)
    {
        $maintenanceRequest->load(['room', 'tenant']);
        return response()->json($maintenanceRequest);
    }

    public function update(Request $request, MaintenanceRequest $maintenanceRequest)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:pending,in_progress,completed,cancelled',
            'resolution_notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'completed_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            if ($request->status === 'completed') {
                $maintenanceRequest->room->update(['status' => 'available']);
                $request->merge(['completed_date' => now()]);
            } elseif ($request->status === 'in_progress' && $maintenanceRequest->status !== 'in_progress') {
                $maintenanceRequest->room->update(['status' => 'maintenance']);
            }

            $maintenanceRequest->update($request->all());

            DB::commit();
            return response()->json($maintenanceRequest);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating maintenance request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(MaintenanceRequest $maintenanceRequest)
    {
        if ($maintenanceRequest->status === 'in_progress') {
            return response()->json([
                'message' => 'Cannot delete in-progress maintenance request'
            ], 422);
        }

        $maintenanceRequest->delete();
        return response()->json(null, 204);
    }

    public function byRoom(Room $room)
    {
        $requests = MaintenanceRequest::where('room_id', $room->id)
            ->latest()
            ->get();

        return response()->json($requests);
    }

    public function statistics()
    {
        $stats = [
            'total' => MaintenanceRequest::count(),
            'pending' => MaintenanceRequest::where('status', 'pending')->count(),
            'in_progress' => MaintenanceRequest::where('status', 'in_progress')->count(),
            'completed' => MaintenanceRequest::where('status', 'completed')->count(),
            'cancelled' => MaintenanceRequest::where('status', 'cancelled')->count(),
            'total_cost' => MaintenanceRequest::where('status', 'completed')->sum('cost'),
            'by_priority' => MaintenanceRequest::select('priority', DB::raw('count(*) as count'))
                ->groupBy('priority')
                ->get()
        ];

        return response()->json($stats);
    }
} 