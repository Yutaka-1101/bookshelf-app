<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class BookFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 書籍登録(Create)
    /** @test */
    public function 認証済みユーザーが書籍を登録でき、登録内容がデータベースに保存される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
        ]);
    }

    /** @test */
    public function 選択したジャンルとの紐づけが作成される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $book = Book::where('isbn', '1234567890000')->first();

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function 書籍登録時に必須項目が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => '',
            'author' => '',
            'isbn' => '',
            'published_date' => '',
            'genres' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function isb_nが重複している場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        Book::factory()->create([
            'isbn' => '1234567890000',
        ]);

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function isb_nが13桁でない場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '123456789000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 存在しないジャンル_i_dを指定した場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [999],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 書籍登録時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Book::creating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->post(route('books.store'), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertStatus(500);
    }

    // 書籍表示(Read)
    /** @test */
    public function 書籍一覧画面が表示され、登録済みの書籍情報が表示される(): void
    {
        $book = Book::factory()->create([
            'title' => 'テストタイトル',
        ]);

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
        $response->assertSee('テストタイトル');
    }

    /** @test */
    public function 書籍に紐づくジャンル情報が表示される(): void
    {
        $genre = Genre::factory()->create([
            'name' => '小説',
        ]);
        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
        $response->assertSee('小説');
    }

    /** @test */
    public function ページネーションが正常に動作する(): void
    {
        Book::factory()->count(11)->create();

        $response = $this->get(route('books.index', ['page' => 2]));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
    }

    /** @test */
    public function 書籍詳細情報が表示される(): void
    {
        $book = Book::factory()->create([
            'title' => 'テストタイトル',
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertSee('テストタイトル');
    }

    /** @test */
    public function 書籍詳細画面でジャンル情報が表示される(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $book->genres()->attach($genre);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertSee('テストジャンル');
    }

    /** @test */
    public function 書籍詳細画面でレビュー一覧が表示される(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
            'comment' => 'テストレビューコメント',
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertSee('テストレビューコメント');
    }

    /** @test */
    public function 書籍詳細画面でレビューに対するいいね数が表示される(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        ReviewLike::factory()->create([
            'review_id' => $review->id,
        ]);

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertSee('1');
    }

    /** @test */
    public function 書籍詳細画面で存在しない書籍_i_dを指定した場合、404になる(): void
    {
        $response = $this->get(route('books.show', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function 書籍一覧表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        View::composer('books.index', function () {
            throw new \Exception;
        });

        $response = $this->get(route('books.index'));

        $response->assertStatus(500);
    }

    /** @test */
    public function 書籍詳細表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $book = Book::factory()->create();

        View::composer('books.show', function () {
            throw new \Exception;
        });

        $response = $this->get(route('books.show', $book));

        $response->assertStatus(500);
    }

    // 書籍編集（Update）
    /** @test */
    public function 既存の書籍情報が書籍編集画面に表示される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタイトル',
        ]);

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(200);
        $response->assertViewIs('books.edit');
        $response->assertSee('テストタイトル');
    }

    /** @test */
    public function 書籍情報を更新すると、更新内容がデータベースに保存される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
        ]);

        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '更新テストタイトル',
            'author' => '更新テスト著者',
            'isbn' => '1234567890011',
            'published_date' => '2026-02-02',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('books', [
            'title' => '更新テストタイトル',
            'author' => '更新テスト著者',
            'isbn' => '1234567890011',
            'published_date' => '2026-02-02',
        ]);
    }

    /** @test */
    public function ジャンル紐づけが更新される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $updateGenre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [$updateGenre->id],
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $updateGenre->id,
        ]);
    }

    /** @test */
    public function 書籍更新時に必須項目が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
        ]);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '',
            'author' => '',
            'isbn' => '',
            'published_date' => '',
            'genres' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時_isb_nが重複している場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'isbn' => '1234567890000',
        ]);
        $duplicateBook = Book::factory()->create([
            'isbn' => '1234567890001',
        ]);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => $duplicateBook->isbn,
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時_isb_nが13桁でない場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'isbn' => '1234567890000',
        ]);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '123456789000',
            'published_date' => '2026-01-01',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時存在しないジャンル_i_dを指定した場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genres' => [999],
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 他ユーザーの書籍編集画面へアクセスすると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('books.edit', $book));

        $response->assertStatus(403);
    }

    /** @test */
    public function 他ユーザーの書籍を更新すると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
            'title' => 'テストタイトル',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
        ]);

        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '更新テストタイトル',
            'author' => '更新テスト著者',
            'isbn' => '1234567890011',
            'published_date' => '2026-02-02',
            'genres' => [$genre->id],
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function 書籍編集時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        Book::updating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->put(route('books.update', $book), [
            'title' => '更新テストタイトル',
            'author' => '更新テスト著者',
            'isbn' => '1234567890011',
            'published_date' => '2026-02-02',
            'genres' => [$genre->id],
        ]);

        $response->assertStatus(500);
    }

    // 書籍削除（Delete）
    /** @test */
    public function 認証済みユーザー本人が書籍を削除でき、データベースから削除される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    /** @test */
    public function 書籍を削除すると紐づくレビューも削除される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    /** @test */
    public function 書籍を削除すると紐づくお気に入りも削除される(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $favorite = Favorite::factory()->create([
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('favorites', [
            'id' => $favorite->id,
        ]);
    }

    /** @test */
    public function 書籍を削除するとジャンルの紐づけが削除される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function 他ユーザーの書籍を削除しようとすると、403になる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertStatus(403);
    }

    /** @test */
    public function 存在しない書籍_i_dを指定した場合、404になる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('books.destroy', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function 書籍削除時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        Book::deleting(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->delete(route('books.destroy', $book));

        $response->assertStatus(500);
    }
}
