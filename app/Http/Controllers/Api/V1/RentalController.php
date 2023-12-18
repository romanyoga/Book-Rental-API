<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\RentalResource;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{

    public function __construct()
    {
        // Add middleware Sanctum and isAdmin
        $this->middleware(['auth:sanctum', 'isAdmin'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rentals = Rental::all();

        return RentalResource::collection($rentals->loadMissing([
            'user:id,name',
            'book:id,title',
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $rental = Rental::create($validated + [
            'rented_at' => now()->format('Y-m-d H:i:s'),
            'due_at' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ]);

        return RentalResource::make($rental);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $rental = Rental::findOrFail($id);

        return new RentalResource($rental->loadMissing([
            'user:id,name',
            'book:id,title',
        ]));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'returned_at' => 'nullable|date_format:Y-m-d H:i:s',
        ]);

        $rental = Rental::findOrFail($id);

        // Update 'returned_at' if in request and not null
        if ($request->has('returned_at') && $request->filled('returned_at')) {
            $rental->returned_at = $request->returned_at;

            // If 'returned_at' filled, set 'is_completed' to true
            $rental->is_completed = true;
        }

        $rental->save();

        return response()->json(['message' => 'Update rent successfully!']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $rental = Rental::findOrFail($id);

        $rental->delete();

        return response()->json(['message' => 'Deleted rent successfully!']);
    }
}
