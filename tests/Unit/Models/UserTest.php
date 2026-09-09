<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function ユーザーから_bookの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->books->contains($book));
    }

    /** @test */
    public function ユーザーから_reviewの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->reviews->contains($review));
    }

    /** @test */
    public function ユーザーから_favoriteの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->favoriteRecords->contains($favorite));
    }

    /** @test */
    public function ユーザーから_review_likeの情報を取得できる(): void
    {
        $user = User::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($user->reviewLikes->contains($reviewLike));
    }

    /** @test */
    public function ユーザーからお気に入り登録した_book一覧を取得できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book->id);

        $this->assertTrue($user->favoriteBooks->contains($book));
    }

    /** @test */
    public function ユーザーからいいねした_review一覧を取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $user->likedReviews()->attach($review->id);

        $this->assertTrue($user->likedReviews->contains($review));
    }
}
