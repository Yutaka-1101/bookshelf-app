<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewLike;
use Illuminate\Http\Request;

class ReviewLikeController extends Controller
{
    public function toggle(Request $request, Review $review)
    {
        $user = $request->user();

        if (
            ReviewLike::where('user_id', $user->id)
                ->where('review_id', $review->id)
                ->exists()
        ) {
            // レビューいいね済みの場合、解除
            ReviewLike::where('user_id', $user->id)
                ->where('review_id', $review->id)
                ->delete();
        } else {
            // 未登録の場合、登録
            ReviewLike::create([
                'user_id' => $user->id,
                'review_id' => $review->id,
            ]);
        }

        return redirect()->route('books.show', $review->book);
    }
}
