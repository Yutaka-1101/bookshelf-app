<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    // 会員登録

    /** @test */
    public function 正しい情報を入力して会員登録でき、情報がデータベースに保存される(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'name' => '山田太郎',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function 必須項目が未入力の場合、バリデーションエラーになる(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function メールアドレスの形式が不正な場合、バリデーションエラーになる(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => '山田太郎',
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 既に登録されているメールアドレスの場合、バリデーションエラーになる(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('register.store'), [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function パスワードと確認用パスワードが一致しない場合は、バリデーションエラーになる(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'defferent-password',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function 会員登録処理中にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        User::creating(function () {
            throw new \Xeception;
        });

        $response = $this->post(route('register.store'), [
            'name' => '山田太郎',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(500);
    }

    // ログイン
    /** @test */
    public function 正しいメールアドレスとパスワードでログインできる(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function メールアドレスまたはパスワードが不正な場合、ログインできない(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /** @test */
    public function ログイン情報が未入力の場合、バリデーションエラーになる(): void
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function ログイン処理中にサーバー側で例外が発生した場合、500エラーが返る(): void
    {
        Auth::shouldReceive('attempt')
            ->andThrow(new \Exception);

        $response = $this->post(route('login'), [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(500);
    }

    // ログアウト
    /** @test */
    public function ログアウトすると認証状態が解除され、ログイン画面へリダイレクトされる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);
        $response = $this->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
