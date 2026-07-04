<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test halaman utama mengalihkan tamu ke halaman login.
     * Aplikasi ini menggunakan redirect (302) bagi pengguna yang belum login.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Halaman utama mengalihkan tamu ke /login (302 Redirect)
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}
