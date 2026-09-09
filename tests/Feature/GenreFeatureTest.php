<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

class GenreFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // ジャンル登録（Create）
    /** @test */
    public function 認証済みユーザーがジャンルを登録でき、データベースに保存される(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => 'テストジャンル',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);
    }

    /** @test */
    public function ジャンル名が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function ジャンル名が20文字を超える場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => str_repeat('あ', 21),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 既に存在するジャンル名を登録した場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => 'テストジャンル',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function ジャンル登録時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();

        Genre::creating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->post(route('genres.store'), [
            'name' => 'テストジャンル',
        ]);

        $response->assertStatus(500);
    }

    // ジャンル表示（Read）
    /** @test */
    public function ジャンル一覧画面が表示される(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function 登録済みジャンル情報が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);
        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200);
        $response->assertSee('テストジャンル');
    }

    /** @test */
    public function ジャンルに紐づく書籍数が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(3)->create();

        $books->each(fn ($book) => $book->genres()->attach($genre));

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(200);
        $response->assertSee('3');
    }

    /** @test */
    public function ジャンル詳細画面が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
    }

    /** @test */
    public function ジャンル詳細画面でジャンル名が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertSee('テストジャンル');
    }

    /** @test */
    public function ジャンル詳細画面でジャンルに紐づく書籍一覧が表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(3)->create();

        $books->each(fn ($book) => $book->genres()->attach($genre));

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertSee($books[0]->title);
        $response->assertSee($books[1]->title);
        $response->assertSee($books[2]->title);
    }

    /** @test */
    public function ジャンルに紐づく書籍のページネーションが正常に動作する(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $books = Book::factory()->count(11)->create();

        $books->each(fn ($book) => $book->genres()->attach($genre));

        $response = $this->actingAs($user)->get(route('genres.show', ['genre' => $genre, 'page' => 2]));

        $response->assertStatus(200);
        $response->assertViewIs('genres.show');
    }

    /** @test */
    public function 存在しないジャンル_i_dを指定した場合、404になる(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('genres.show', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function ジャンル一覧表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        View::composer('genres.index', function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->get(route('genres.index'));

        $response->assertStatus(500);
    }

    /** @test */
    public function ジャンル詳細表示時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        View::composer('genres.show', function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->get(route('genres.show', $genre));

        $response->assertStatus(500);
    }

    // ジャンル編集（Update）
    /** @test */
    public function 認証済みユーザーがジャンル編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
    }

    /** @test */
    public function 既存のジャンル名が編集画面に表示される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
        $response->assertSee('テストジャンル');
    }

    /** @test */
    public function 認証済みユーザーがジャンル名を更新でき、データベースに保存される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '更新ジャンル',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('genres', [
            'name' => '更新ジャンル',
        ]);
    }

    /** @test */
    public function 更新時ジャンル名が未入力の場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 更新時ジャンル名が20文字を超える場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => str_repeat('あ', 21),
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 既に存在するジャンル名へ変更した場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);
        $otherGenre = Genre::factory()->create([
            'name' => '別ジャンル',
        ]);

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '別ジャンル',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function ジャンル編集時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => 'テストジャンル',
        ]);

        Genre::updating(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->put(route('genres.update', $genre), [
            'name' => '更新ジャンル',
        ]);

        $response->assertStatus(500);
    }

    /** @test */
    public function 認証済みユーザーがジャンルを削除でき、データベースから削除される(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect();
        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
        ]);
    }

    /** @test */
    public function 書籍が紐づいているジャンルを削除した場合、削除できない(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect();
        $response->assertSessionHas('error', '書籍が紐づいているため削除できません');
    }

    /** @test */
    public function ジャンル削除時に存在しないジャンル_i_dを指定した場合、404になる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete(route('genres.destroy', 999));

        $response->assertStatus(404);
    }

    /** @test */
    public function ジャンル削除時にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        Genre::deleting(function () {
            throw new \Exception;
        });

        $response = $this->actingAs($user)->delete(route('genres.destroy', $genre));

        $response->assertStatus(500);
    }
}
