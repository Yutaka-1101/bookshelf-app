<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function お気に入りから登録ユーザー情報を取得できる(): void
    {
        $user = User::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($favorite->user->is($user));
    }

    /** @test */
    public function お気に入りから対象書籍情報を取得できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();
        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $this->assertTrue($favorite->book->is($book));
    }
}
