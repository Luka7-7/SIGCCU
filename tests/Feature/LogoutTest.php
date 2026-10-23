<?php

namespace Tests\Feature;

use App\Models\Usuario;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    public function test_login_page_is_not_cached_with_a_stale_csrf_token(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_authenticated_user_can_log_out(): void
    {
        $this->actingAs(new Usuario(['id' => 1]));

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
