<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
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
        $books = Book::all();

        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:150',
            'author' => 'required|max:50',
            'description' => 'required',
            'quantity' => 'required|integer',
        ]);

        $user = Auth::user();

        if ($user->is_admin !== 1) {
            return response()->json(['message' => 'Unauthorized. Only admin can store books.', 403]);
        }

        $book = Book::create($validated);

        return BookResource::make($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::findOrFail($id);

        return response()->json($book);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $book = Book::findOrFail($id);
        $book->update($request->all());

        return response()->json(['message' => 'Update task successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::findOrFail($id);

        $book->delete();

        return response()->json(['message' => 'Deleted successfully!']);
    }
}
