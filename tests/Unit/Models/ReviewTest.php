<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Review;
use App\Models\User;
use App\Models\Book;
use App\Models\ReviewLike;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    use RefreshDatabase;

    /** @test */
    public function レビューから投稿ユーザーの情報を取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($review->user->is($user));
    }

    /** @test */
    public function レビューから書籍情報を取得できる(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($review->book->is($book));
    }

    /** @test */
    public function レビューからいいね一覧を取得できる(): void
    {
        $review = Review::factory()->create();
        $reviewLike = ReviewLike::factory()->create([
            'review_id' => $review->id,
        ]);

        $this->assertTrue($review->reviewLikes->contains($reviewLike));
    }

    /** @test */
    public function レビューからいいねしたユーザー一覧を取得できる(): void
    {
        $review = Review::factory()->create();
        $user = User::factory()->create();
        $reviewLike = ReviewLike::factory()->create([
            'review_id' => $review->id,
            'user_id' => $user->id,
        ]);

        $this->assertTrue($review->likedByUsers->contains($user));
    }
}
