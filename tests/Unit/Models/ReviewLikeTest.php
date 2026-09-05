<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\ReviewLike;
use App\Models\User;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewLikeTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    use RefreshDatabase;

    /** @test */
    public function いいねから登録ユーザー情報を取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $this->assertTrue($reviewLike->user->is($user));
    }

    /** @test */
    public function いいねから対象レビュー情報を取得できる(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $this->assertTrue($reviewLike->review->is($review));
    }
}
