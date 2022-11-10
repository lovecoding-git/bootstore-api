<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.verify', ['except' => ['index']]);
    }

    public function index(Request $request)
    {
        try {
            $query = Book::published();

            if($request->keyword)
            {
                $keyword = $request->keyword;

                $query = $query->where(function($q) use($keyword) {
                    $q->where("title", "LIKE", "%$keyword%");
                    $q->orWhere("description", "LIKE", "%$keyword%");
                });
            }

            $books = $query->get();

            return response()->preferredFormat([
                'status' => 'success',
                'books' => $books->toArray(),
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:600',
                'image' => 'nullable|string|max:255',
                'price' => 'required|string|regex:/^\d+(\.\d{1,2})?$/', //two decimal points
            ]);

            $user = auth()->user();

            $published = 1;
            if($user->name === 'Darth Vader') //or we can put any other logic here
            {
                $published = 0;
            }

            $book = Book::create([
                'title' => $request->title,
                'user_id' => $user->id,
                'description' => $request->description,
                'price' => $request->price,
                'image' => $request->image,
                'published' => $published,
            ]);

            return response()->preferredFormat([
                'status' => 'success',
                'message' => 'Book created successfully',
                'book' => $book->toArray(),
            ]);
        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function show($id)
    {
        try {

            $book = Book::my()->where("id", $id)->firstorfail();

            return response()->preferredFormat([
                'status' => 'success',
                'book' => $book->toArray(),
            ]);

        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:600',
                'image' => 'nullable|string|max:255',
                'price' => 'required|string|regex:/^\d+(\.\d{1,2})?$/', //two decimal points
            ]);

            $book = Book::my()->where("id", $id)->firstorfail();
            $book->title = $request->title;
            $book->description = $request->description;
            $book->image = $request->image;
            $book->price = $request->price;
            $book->save();

            return response()->preferredFormat([
                'status' => 'success',
                'message' => 'Book updated successfully',
                'book' => $book->toArray(),
            ]);

        }catch(\Exception $e)
        {
            return response()->preferredFormat([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function unpublish($id)
    {
        try {
            $book = Book::my()->where("id", $id)->firstorfail();
            $book->published = 0;
            $book->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Book unpublished successfully',
                'book' => $book,
            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $book = Book::my()->where("id", $id)->firstorfail();
            $book->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Book deleted successfully',
                'book' => $book,
            ]);
        }catch(\Exception $e)
        {
            return response()->json([
                'status' => 'false',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
