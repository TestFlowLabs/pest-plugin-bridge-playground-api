<?php

use App\Models\User;

it('can register a new user', function () {
    $this->bridge('/register')
        ->fill('[data-testid="name-input"]', 'John Doe')
        ->fill('[data-testid="email-input"]', 'john@example.com')
        ->fill('[data-testid="password-input"]', 'password123')
        ->fill('[data-testid="password-confirmation-input"]', 'password123')
        ->click('[data-testid="register-button"]')
        ->waitForPath('/dashboard')
        ->assertPathContains('/dashboard')
        ->assertVisible('[data-testid="user-info"]')
        ->assertSeeIn('[data-testid="user-name"]', 'John Doe');

    expect(User::where('email', 'john@example.com')->exists())->toBeTrue();
});
