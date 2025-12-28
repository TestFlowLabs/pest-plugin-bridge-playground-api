<?php

use App\Models\User;

it('can login with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password123'),
    ]);

    $this->bridge('/login')
        ->fill('[data-testid="email-input"]', 'test@example.com')
        ->fill('[data-testid="password-input"]', 'password123')
        ->click('[data-testid="login-button"]')
        ->waitForPath('/dashboard')
        ->assertPathContains('/dashboard')
        ->assertVisible('[data-testid="user-info"]')
        ->assertSeeIn('[data-testid="user-name"]', $user->name);
});

it('shows error with invalid credentials', function () {
    $this->bridge('/login')
        ->fill('[data-testid="email-input"]', 'wrong@example.com')
        ->fill('[data-testid="password-input"]', 'wrongpassword')
        ->click('[data-testid="login-button"]')
        ->waitForVisible('[data-testid="error-message"]')
        ->assertVisible('[data-testid="error-message"]')
        ->assertPathContains('/login');
});
