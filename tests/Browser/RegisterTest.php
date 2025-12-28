<?php

it('can register a new user', function () {
    $email = 'register'.time().'@example.com';

    $this->bridge('/register')
        ->waitForEvent('networkidle')
        ->click('input#name')
        ->typeSlowly('input#name', 'NewUser', 30)
        ->typeSlowly('input#email', $email, 20)
        ->typeSlowly('input#password', 'password123', 20)
        ->typeSlowly('input#password_confirmation', 'password123', 20)
        ->click('button[type="submit"]')
        ->waitForEvent('networkidle')
        ->assertPathContains('/dashboard')
        ->assertSee('Welcome');
});
