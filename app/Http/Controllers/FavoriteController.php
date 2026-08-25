<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Book;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $books = $request->user()
            ->favoriteBooks()
            ->paginate(10);

        return view('favorites.index', compact('books'));
    }

    public function toggle(Request $request, Book $book)
    {
        $user = $request->user();

        if (
            Favorite::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->exists()
        ) {
            //お気に入り済みの場合->解除
            Favorite::where('user_id', $user->id)
                ->where('book_id', $book->id)
                ->delete();
        } else {
            //未登録の場合->登録
            Favorite::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
        }

        return redirect()->route('books.show', $book);
    }
}
