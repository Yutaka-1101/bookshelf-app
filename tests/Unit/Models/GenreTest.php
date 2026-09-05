<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Genre;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenreTest extends TestCase
{
    /**
     * A basic unit test example.
     */

    use RefreshDatabase;

    /** @test */
    public function ジャンルからBook一覧を取得できる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $genre->books()->attach($book->id);

        $this->assertTrue($genre->books->contains($book));
    }
}
