<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class RankingFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    /** @test */
    public function レビューがある書籍のみランキング表示される(): void
    {
        $bookWithReview = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $bookWithReview->id,
        ]);
        $bookWithoutReview = Book::factory()->create();

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ranking.index');
        $response->assertSee($bookWithReview->title);
        $response->assertDontSee($bookWithoutReview->title);
    }

    /** @test */
    public function レビュー平均評価が高い順に表示される(): void
    {
        $hiRateBook = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $hiRateBook->id,
            'rating' => 5,
        ]);
        $lowRateBook = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $lowRateBook->id,
            'rating' => 3,
        ]);

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertSeeInOrder([
            $hiRateBook->title,
            $lowRateBook->title,
        ]);
    }

    /** @test */
    public function 上位10件の書籍が表示される(): void
    {
        $books = Book::factory()->count(11)->create();
        foreach ($books as $index => $book) {
            Review::factory()->create([
                'book_id' => $book->id,
                'rating' => $index === 10 ? 1 : 5,
            ]);
        }

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ranking.index');

        $rankedBooks = $response->viewData('rankedBooks');

        $this->assertCount(10, $rankedBooks);
        $this->assertTrue($rankedBooks->contains('id', $books[0]->id));
        $this->assertFalse($rankedBooks->contains('id', $books[10]->id));
    }

    /** @test */
    public function ランキング表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        View::composer('ranking.index', function () {
            throw new \Exception;
        });

        $response = $this->get(route('ranking.index'));

        $response->assertStatus(500);
    }
}
