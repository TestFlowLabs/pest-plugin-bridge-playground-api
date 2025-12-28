<?php

// Note: Complex multi-step tests with multiple bridge() calls have a known issue
// where the first few characters typed get lost. For now, we test login error handling.

it('shows error with invalid credentials', function () {
    $this->bridge('/login')
        ->waitForEvent('networkidle')
        ->click('input#email')
        ->wait(0.5) // Wait for focus
        ->typeSlowly('input#email', 'nonexistent@example.com', 30)
        ->typeSlowly('input#password', 'wrongpassword', 30)
        ->click('button[type="submit"]')
        ->waitForEvent('networkidle')
        ->assertSee('The provided credentials are incorrect')
        ->assertPathContains('/login');
});

it('displays login form correctly', function () {
    $this->bridge('/login')
        ->waitForEvent('networkidle')
        ->assertSee('Login')
        ->assertSee('Email')
        ->assertSee('Password')
        ->assertSee('Register here');
});
