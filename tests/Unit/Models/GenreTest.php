<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    /** @test */
    public function ジャンルから_book一覧を取得できる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $genre->books()->attach($book->id);

        $this->assertTrue($genre->books->contains($book));
    }
}
