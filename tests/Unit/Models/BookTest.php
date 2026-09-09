<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function 書籍から登録ユーザー情報を取得できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($book->user->is($user));
    }

    /** @test */
    public function 書籍からレビュー一覧を取得できる(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($book->reviews->contains($review));
    }

    /** @test */
    public function 書籍からジャンル一覧を取得できる(): void
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre->id);

        $this->assertTrue($book->genres->contains($genre));
    }

    /** @test */
    public function 書籍からお気に入り一覧を取得できる(): void
    {
        $book = Book::factory()->create();
        $favorite = Favorite::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($book->favorites->contains($favorite));
    }
}
