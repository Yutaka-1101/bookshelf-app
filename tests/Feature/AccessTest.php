<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // ゲストアクセス可能な画面
    /** @test */
    public function ゲストユーザーが書籍一覧画面にアクセスすると、200レスポンスが返り、書籍一覧が表示される(): void
    {
        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
    }

    /** @test */
    public function ゲストユーザーが書籍詳細画面へアクセスすると、200レスポンスが返り、書籍情報が表示される(): void
    {
        $book = Book::factory()->create();
        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
    }

    /** @test */
    public function ゲストユーザーがランキング画面へアクセスすると、200レスポンスが返り、ランキング情報が表示される(): void
    {
        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ranking.index');
    }

    // ログイン必須画面
    /** @test */
    public function ゲストユーザーが書籍登録画面へアクセスするとログイン画面へリダイレクトされる(): void
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ゲストユーザーがお気に入り一覧へアクセスするとログイン画面へリダイレクトされる(): void
    {
        $response = $this->get(route('favorites.index'));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function ゲストユーザーがジャンル画面へアクセスするとログイン画面へリダイレクトされる(): void
    {
        $response = $this->get(route('genres.index'));

        $response->assertRedirect(route('login'));
    }

    // 本人確認が必要な画面
    /** @test */
    public function ログインユーザー本人は書籍編集画面へアクセスできる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.edit');
    }

    /** @test */
    public function 他ユーザーの書籍編集画面へアクセスすると403になる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(403);
    }

    /** @test */
    public function ログインユーザー本人はレビュー編集画面へアクセスできる(): void
    {
        $user = User::factory()->create();
        $review = review::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        $response->assertStatus(200);
        $response->assertViewIs('reviews.edit');
    }

    /** @test */
    public function 他ユーザーのレビュー編集画面へアクセスすると403になる(): void
    {
        $user = User::factory()->create();
        $review = review::factory()->create();

        $response = $this->actingAs($user)->get(route('reviews.edit', $review));

        $response->assertStatus(403);
    }
}
