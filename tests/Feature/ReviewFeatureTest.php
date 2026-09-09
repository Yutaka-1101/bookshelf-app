<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function 認証済みユーザーはレビューを投稿でき、データベースに保存される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response->assertRedirect();
        $this->assertDatabasehas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);
    }

    /** @test */
    public function 必須項目が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => '',
            'comment' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 評価が1～5以外の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => 6,
            'comment' => 'テストレビューコメント',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function コメントが255文字を超えた場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => str_repeat('あ', 256),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 未認証ユーザーがレビュー投稿するとログイン画面へリダイレクトされる(): void
    {
        $book = Book::factory()->create();

        $response = $this->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function レビュー投稿時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Review::creating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->post(route('reviews.store', $book), [
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response->assertStatus(500);
    }

    // レビュー表示（Read）
    /** @test */
    public function 書籍詳細画面でレビュー投稿者情報が表示される(): void
    {
        $user = User::factory()->create([
            'name' => '山田太郎',
        ]);
        $book = Book::factory()->create();

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSee('山田太郎');
    }

    /** @test */
    public function 書籍詳細画面で評価（rating）が表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSee('5');
    }

    /** @test */
    public function 書籍詳細画面でコメントが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSee('テストレビューコメント');
    }

    /** @test */
    public function 書籍詳細画面でいいね数が表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSee('1');
    }

    // レビュー編集（Update）
    /** @test */
    public function 既存の評価（rating）とコメントが表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        $response->assertStatus(200);
        $response->assertSee('5');
        $response->assertSee('テストレビューコメント');
    }

    /** @test */
    public function 認証済みユーザー本人がレビューを更新でき、データベースに保存される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 3,
            'comment' => '更新レビューコメント',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 3,
            'comment' => '更新レビューコメント',
        ]);
    }

    /** @test */
    public function レビュー更新時必須項目が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => '',
            'comment' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時評価が1～5以外の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 6,
            'comment' => 'テストレビューコメント',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時コメントが255文字を超えた場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 5,
            'comment' => str_repeat('あ', 256),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 他ユーザーのレビュー編集画面へアクセスすると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        $response->assertStatus(403);
    }

    /** @test */
    public function 他ユーザーのレビューを更新すると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 3,
            'comment' => '更新レビューコメント',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function レビュー編集時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'rating' => 5,
            'comment' => 'テストレビューコメント',
        ]);

        Review::updating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->put(route('reviews.update', $review), [
            'rating' => 3,
            'comment' => '更新レビューコメント',
        ]);

        $response->assertStatus(500);
    }

    // レビュー削除（Delete）
    /** @test */
    public function 認証済みユーザー本人がレビューを削除でき、データベースから削除できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    /** @test */
    public function レビュー削除時にレビューといいねの紐づけが削除される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        ReviewLike::factory()->create([
            'review_id' => $review->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertRedirect();
        $this->assertDatabaseMissing('review_likes', [
            'review_id' => $review->id,
        ]);
    }

    /** @test */
    public function 他ユーザーのレビューを削除しようとすると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertStatus(403);
    }

    /** @test */
    public function レビュー削除時に存在しないレビュー_i_dを指定すると、404になる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('reviews.destroy', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function レビュー削除時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        Review::deleting(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->delete(route('reviews.destroy', $review));

        $response->assertStatus(500);
    }
}
