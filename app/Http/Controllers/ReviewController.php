<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\Book;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReviewRequest $request, Book $book)
    {
        $userId = $request->user()->id;
        $bookId = $book->id;

        $validated = $request->validated();

        Review::create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを投稿しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $this->authorize('update', $review);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validated();

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()
            ->route('books.show', $review->book)
            ->with('success', 'レビューを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $book = $review->book;

        $review->delete();

        return redirect()
            ->route('books.show', $book)
            ->with('success', 'レビューを削除しました');
    }
}
