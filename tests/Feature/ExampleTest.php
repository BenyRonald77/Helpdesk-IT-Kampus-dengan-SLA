<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Pengguna belum login yang membuka root diarahkan ke halaman login
     * (aplikasi ini adalah alat kerja internal, bukan halaman publik).
     */
    public function test_a_guest_visiting_root_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
