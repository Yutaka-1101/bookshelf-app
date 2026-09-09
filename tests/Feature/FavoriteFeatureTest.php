<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class FavoriteFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // お気に入り登録・削除
    /** @test */
    public function 認証済みユーザーがお気に入り登録でき、データベースに保存される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $response->assertRedirect();
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /** @test */
    public function お気に入り済みの投稿を再度操作すると、お気に入りが解除され、データベースから削除される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /** @test */
    public function お気に入り登録・解除操作を連続して実行しても、重複登録されず正しい状態になる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));
        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));
        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $this->assertDatabaseCount('favorites', 1);
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);
    }

    /** @test */
    public function 未認証ユーザーがお気に入り登録を実行しようとするとログイン画面へリダイレクトされる(): void
    {
        $book = Book::factory()->create();

        $response = $this->post(route('favorites.toggle', $book));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 存在しない書籍_i_dを指定した場合、404になる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('favorites.toggle', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function お気に入り登録時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        Favorite::creating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->post(route('favorites.toggle', $book));

        $response->assertStatus(500);
    }

    // お気に入り一覧
    /** @test */
    public function 認証済みユーザーがお気に入り一覧を表示できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertViewIs('favorites.index');
    }

    /** @test */
    public function 自分がお気に入り登録した書籍のみ表示される(): void
    {
        $user = User::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $otherUser = User::factory()->create();
        $otherFavorite = Favorite::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertSee($favorite->book->title);
        $response->assertDontSee($otherFavorite->book->title);
    }

    /** @test */
    public function お気に入り書籍が10件でページでページネーションされる(): void
    {
        $user = User::factory()->create();
        $favorite = Favorite::factory()->count(11)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('favorites.index', ['page' => 2]));

        $response->assertStatus(200);
    }

    /** @test */
    public function 未認証ユーザーがお気に入り一覧へアクセスするとログイン画面へリダイレクトされる(): void
    {
        $response = $this->get(route('favorites.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function お気に入り一覧表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();

        View::composer('favorites.index', function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(500);
    }
}
