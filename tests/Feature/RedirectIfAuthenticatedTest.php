<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    /** @test */
    public function 認証済みユーザーがログイン画面にアクセスするとホーム画面へリダイレクトされる(): void
    {
        $middleware = new RedirectIfAuthenticated;
        $request = Request::create('/login', 'GET');
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $middleware->handle($request, fn() => response('next'));

        $this->assertEquals(
            url(RouteServiceProvider::HOME),
            $response->headers->get('Location')
        );

        Auth::logout();
    }

    /** @test */
    public function 未認証ユーザーはログイン画面へアクセスできる(): void
    {
        $middleware = new RedirectIfAuthenticated;
        $request = Request::create('/login', 'GET');

        $response = $middleware->handle($request, fn() => response('allowed'));

        $this->assertEquals('allowed', $response->getContent());
    }
}
