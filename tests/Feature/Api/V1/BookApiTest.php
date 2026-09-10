<?php

namespace Tests\Feature\Api\V1;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 書籍一覧API

    /** @test */
    public function 書籍一覧_ap_iが_jso_n形式で返り、ジャンル情報、平均評価、レビュー件数を取得できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
        ]);

        $response = $this->getJson('/api/v1/books');

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'title',
                    'author',
                    'isbn',
                    'published_date',
                    'description',
                    'image_url',
                    'genres',
                    'average_rating',
                    'reviews_count',
                ],
            ],
        ]);

        $response->assertJsonPath('data.0.id', $book->id);
        $response->assertJsonPath('data.0.genres.0.id', $genre->id);
        $response->assertJsonPath('data.0.average_rating', '5.0000');
        $response->assertJsonPath('data.0.reviews_count', 1);
    }

    /** @test */
    public function キーワードでタイトル・著者を絞り込み検索できる(): void
    {
        $titleBook = Book::factory()->create([
            'title' => '夏目漱石の作品集',
        ]);

        $authorBook = Book::factory()->create([
            'author' => '夏目漱石',
        ]);

        Book::factory()->create([
            'title' => '吾輩は猫ではない',
            'author' => '別の著者',
        ]);

        $response = $this->getJson('/api/v1/books?keyword=夏目漱石');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment([
            'id' => $titleBook->id,
        ]);
        $response->assertJsonFragment([
            'id' => $authorBook->id,
        ]);
    }

    /** @test */
    public function ジャンル_i_dで絞り込みできる(): void
    {
        $genre = Genre::factory()->create();

        $targetBook = Book::factory()->create();
        $targetBook->genres()->attach($genre);

        Book::factory()->create();

        $response = $this->getJson('/api/v1/books?genre_id='.$genre->id);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment([
            'id' => $targetBook->id,
        ]);
    }

    /** @test */
    public function ページ番号、ページあたりの書籍件数を指定してページネーションできる(): void
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/books?page=2&per_page=2');

        $response->assertOk();
        $response->assertJsonPath('meta.current_page', 2);
        $response->assertJsonPath('meta.per_page', 2);
        $response->assertJsonPath('meta.total', 5);
    }

    /** @test */
    public function 存在しないジャンル_i_dを指定すると422になる(): void
    {
        $response = $this->getJson('/api/v1/books?genre_id=99999');

        $response->assertStatus(422);
    }

    /** @test */
    public function ページ数を1未満に指定すると422になる(): void
    {
        $response = $this->getJson('/api/v1/books?page=0');

        $response->assertStatus(422);
    }

    /** @test */
    public function ページあたりの書籍件数を1未満に指定すると422になる(): void
    {
        $response = $this->getJson('/api/v1/books?per_page=0');

        $response->assertStatus(422);
    }

    /** @test */
    public function ページあたりの書籍件数を101以上に指定すると422になる(): void
    {
        $response = $this->getJson('/api/v1/books?per_page=101');

        $response->assertStatus(422);
    }

    /** @test */
    public function 書籍一覧_ap_iでサーバー側に例外が発生した場合、500エラーが返る(): void
    {
        $originalResolver = Book::getConnectionResolver();

        $resolver = Mockery::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('connection')
            ->andThrow(new \Exception);

        Book::setConnectionResolver($resolver);

        try {
            $response = $this->getJson('/api/v1/books');

            $response->assertStatus(500);
        } finally {
            Book::setConnectionResolver($originalResolver);
        }
    }

    // 書籍詳細API

    /** @test */
    public function 書籍詳細_ap_iが_jso_nレスポンスで返り、レビュー情報（投稿者名、評価、コメント、投稿日時）を取得できる(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 5,
            'comment' => 'テストコメント',
        ]);

        $response = $this->getJson('/api/v1/books/'.$book->id);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'author',
                'isbn',
                'published_date',
                'description',
                'image_url',
                'genres',
                'average_rating',
                'reviews_count',
                'reviews' => [
                    '*' => [
                        'id',
                        'user_name',
                        'rating',
                        'comment',
                        'created_at',
                    ],
                ],
            ],
        ]);

        $response->assertJsonPath('data.id', $book->id);
        $response->assertJsonPath('data.reviews.0.user_name', $user->name);
        $response->assertJsonPath('data.reviews.0.rating', 5);
        $response->assertJsonPath('data.reviews.0.comment', 'テストコメント');
    }

    /** @test */
    public function 存在しない書籍_i_dを指定すると404になる(): void
    {
        $response = $this->getJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }

    /** @test */
    public function 書籍詳細_ap_iでサーバー側に例外が発生した場合、500エラーが返る(): void
    {
        $book = Book::factory()->create();

        $originalResolver = Book::getConnectionResolver();

        $resolver = Mockery::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('connection')
            ->andThrow(new \Exception);

        Book::setConnectionResolver($resolver);

        try {
            $response = $this->getJson('/api/v1/books/'.$book->id);

            $response->assertStatus(500);
        } finally {
            Book::setConnectionResolver($originalResolver);
        }
    }

    // 書籍登録API
    /** @test */
    public function 登録成功時に201を返し、_jso_n形式で登録内容を取得できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
            'description' => 'テスト説明',
            'image_url' => 'http://example.com/image.jpg',
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'author',
                'isbn',
                'published_date',
                'description',
                'image_url',
                'genres',
                'average_rating',
                'reviews_count',
            ],
        ]);

        $response->assertJsonPath('data.title', 'テスト書籍');
        $response->assertJsonPath('data.author', 'テスト著者');
        $response->assertJsonPath('data.isbn', '1234567890000');
    }

    /** @test */
    public function 必須項目が未入力の場合、422になる(): void
    {
        $response = $this->postJson('/api/v1/books', []);

        $response->assertStatus(422);
    }

    /** @test */
    public function titleが255文字を超える場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => str_repeat('あ', 256),
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function authorが40文字を超える場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => str_repeat('あ', 41),
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function isb_nが13桁でない場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '12345678900000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function isb_nが重複している場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        Book::factory()->create([
            'isbn' => '1234567890000',
        ]);

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 出版日が日付形式でない場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => 'not-a-date',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function ジャンル_i_dが配列でない場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => $genre->id,
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function ジャンル_i_dが1件未満の場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 存在しないジャンル_i_dを指定した場合、422になる(): void
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [999],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function イメージ_ur_lが_ur_l形式でない場合、422になる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [$genre->id],
            'image_url' => 'not-a-url',
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 書籍登録_ap_iでサーバー側に例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $originalResolver = Book::getConnectionResolver();

        $resolver = Mockery::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('connection')
            ->andThrow(new \Exception);

        Book::setConnectionResolver($resolver);

        try {
            $response = $this->postJson('/api/v1/books', [
                'user_id' => $user->id,
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
                'isbn' => '1234567890000',
                'published_date' => '2026-01-01',
                'genre_ids' => [$genre->id],
            ]);

            $response->assertStatus(500);
        } finally {
            Book::setConnectionResolver($originalResolver);
        }
    }

    // 書籍更新API
    /** @test */
    public function 更新成功時に200を返し、_jso_n形式で登録内容を取得できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => '更新テスト書籍',
            'author' => '更新テスト著者',
            'isbn' => '1234567890001',
            'published_date' => '2026-02-02',
            'genre_ids' => [$genre->id],
            'description' => '更新テスト説明',
            'image_url' => 'http://example.com/update.jpg',
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'author',
                'isbn',
                'published_date',
                'description',
                'image_url',
                'genres',
                'average_rating',
                'reviews_count',
            ],
        ]);

        $response->assertJsonPath('data.title', '更新テスト書籍');
        $response->assertJsonPath('data.author', '更新テスト著者');
        $response->assertJsonPath('data.isbn', '1234567890001');
    }

    /** @test */
    public function 自身の_isb_nを指定しても422にならず更新できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
            'isbn' => '1234567890000',
        ]);

        $data = [
            'title' => '更新テスト書籍',
            'author' => '更新テスト著者',
            'isbn' => $book->isbn,
            'published_date' => '2026-02-02',
            'genre_ids' => [$genre->id],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);
        $response->assertJsonPath('data.isbn', $book->isbn);
    }

    /** @test */
    public function 更新時存在しない書籍_i_dを指定すると404になる(): void
    {
        Book::factory()->create();

        $response = $this->putJson('/api/v1/books/999', [
            'title' => '更新テスト書籍',
            'author' => '更新テスト著者',
            'isbn' => '1234567890001',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
        ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function 更新時必須項目が未入力の場合、422になる(): void
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/v1/books/{$book->id}", []);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時titleが255文字を超える場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => str_repeat('あ', 256),
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時authorが40文字を超える場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => str_repeat('あ', 41),
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時_isb_nが13桁でない場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '12345678900000',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時_isb_nが重複している場合、422になる(): void
    {
        $book = Book::factory()->create();
        $otherBook = Book::factory()->create([
            'isbn' => '1234567890001',
        ]);

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890001',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時出版日が日付形式でない場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => 'not-a-date',
            'genre_ids' => [1],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時ジャンル_i_dが配列でない場合、422になる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => $genre->id,
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時ジャンル_i_dが1件未満の場合、422になる(): void
    {
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時存在しないジャンル_i_dを指定した場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [999],
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 更新時イメージ_ur_lが_ur_l形式でない場合、422になる(): void
    {
        $book = Book::factory()->create();

        $data = [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890000',
            'published_date' => '2026-01-01',
            'genre_ids' => [1],
            'image_url' => 'not-a-url',
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function 書籍更新_ap_iでサーバー側に例外が発生した場合500エラーが返る(): void
    {
        $book = Book::factory()->create();

        $originalResolver = Book::getConnectionResolver();

        $resolver = Mockery::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('connection')
            ->andThrow(new \Exception);

        Book::setConnectionResolver($resolver);

        try {
            $response = $this->putJson("/api/v1/books/{$book->id}", [
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
                'isbn' => '1234567890000',
                'published_date' => '2026-01-01',
                'genre_ids' => [1],
            ]);

            $response->assertStatus(500);
        } finally {
            Book::setConnectionResolver($originalResolver);
        }
    }

    // 書籍削除API
    /** @test */
    public function 書籍削除した場合、204を返し、データベースから書籍と関連するレビュー、お気に入り、ジャンルとの紐づけが削除される(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);
        $favorite = Favorite::factory()->create([
            'book_id' => $book->id,
        ]);
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre->id);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
        ]);
        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    /** @test */
    public function 削除時存在しない書籍_i_dを指定すると404になる(): void
    {
        $response = $this->deleteJson('/api/v1/books/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function 書籍削除_ap_iでサーバー側に例外が発生した場合500エラーが返る(): void
    {
        $book = Book::factory()->create();

        $originalResolver = Book::getConnectionResolver();

        $resolver = Mockery::mock(ConnectionResolverInterface::class);
        $resolver->shouldReceive('connection')
            ->andThrow(new \Exception);

        Book::setConnectionResolver($resolver);

        try {
            $response = $this->deleteJson("/api/v1/books/{$book->id}");

            $response->assertStatus(500);
        } finally {
            Book::setConnectionResolver($originalResolver);
        }
    }
}
