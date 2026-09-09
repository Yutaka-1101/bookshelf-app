<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーがレビューにいいねでき、データベースに保存される(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseHas('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function いいね済みのレビューを再度操作すると、いいねが解除され、データベースから削除される(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();
        ReviewLike::factory()->create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)->post(route('reviews.like', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('review_likes', [
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function 未認証ユーザーがいいねを実行するとログイン画面へリダイレクトされる(): void
    {
        $review = Review::factory()->create();

        $response = $this->post(route('reviews.like', $review));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 存在しないレビュー_i_dを指定した場合、404になる(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post(route('reviews.like', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function レビューいいね時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create();

        ReviewLike::creating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->post(route('reviews.like', $review));

        $response->assertStatus(500);
    }
}
