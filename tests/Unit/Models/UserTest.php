<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;
use App\Models\Favorite;
use App\Models\ReviewLike;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    use RefreshDatabase;

    /** @test */
    public function ユーザーからBookの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->books->contains($book));
    }

    /** @test */
    public function ユーザーからReviewの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->reviews->contains($review));
    }

    /** @test */
    public function ユーザーからFavoriteの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->favoriteRecords->contains($favorite));
    }

    /** @test */
    public function ユーザーからReviewLikeの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->reviewLikes->contains($reviewLike));
    }

    /** @test */
    public function ユーザーからお気に入り登録したBook一覧を取得できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    /** @test */
    public function ユーザーからいいねしたReview一覧を取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likedReviews()->attach($review->id);

        $this->assertTrue($user->likedReviews->contains($review));
    }
}
