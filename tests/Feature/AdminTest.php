<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_login_page_loads(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@po-in-aja.com')->first();
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_orders(): void
    {
        $user = User::where('email', 'admin@po-in-aja.com')->first();
        $response = $this->actingAs($user)->get('/admin/orders');
        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_access_campaigns(): void
    {
        $user = User::where('email', 'admin@po-in-aja.com')->first();
        $response = $this->actingAs($user)->get('/admin/campaigns');
        $response->assertStatus(200);
    }
}
