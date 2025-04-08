<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with(['currentContract.tenant'])->get();
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|unique:rooms',
            'floor' => 'required',
            'type' => 'required',
            'rent_amount' => 'required|numeric|min:0',
            'description' => 'nullable',
            'amenities' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::create($request->all());
        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        $room->load(['currentContract.tenant', 'maintenanceRequests' => function($query) {
            $query->latest()->take(5);
        }]);
        return response()->json($room);
    }

    public function update(Request $request, Room $room)
    {
        $validator = Validator::make($request->all(), [
            'room_number' => 'required|unique:rooms,room_number,' . $room->id,
            'floor' => 'required',
            'type' => 'required',
            'rent_amount' => 'required|numeric|min:0',
            'status' => 'required|in:available,occupied,maintenance',
            'description' => 'nullable',
            'amenities' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room->update($request->all());
        return response()->json($room);
    }

    public function destroy(Room $room)
    {
        if ($room->currentContract) {
            return response()->json([
                'message' => 'Cannot delete room with active contract'
            ], 422);
        }

        $room->delete();
        return response()->json(null, 204);
    }

    public function statistics()
    {
        $stats = [
            'total' => Room::count(),
            'available' => Room::where('status', 'available')->count(),
            'occupied' => Room::where('status', 'occupied')->count(),
            'maintenance' => Room::where('status', 'maintenance')->count(),
            'occupancy_rate' => Room::where('status', 'occupied')->count() / Room::count() * 100
        ];

        return response()->json($stats);
    }
} 