# Pest Plugin Bridge - Playground API

This is a Laravel API project used as a playground for testing [pest-plugin-bridge](https://github.com/TestFlowLabs/pest-plugin-bridge) with an external Nuxt frontend.

## Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     Test Execution Flow                          │
├─────────────────────────────────────────────────────────────────┤
│  Laravel API (port 8000)     Nuxt Frontend (port 3000)          │
│  ├─ /api/register            ├─ /register                       │
│  ├─ /api/login               ├─ /login                          │
│  ├─ /api/logout              ├─ /dashboard                      │
│  └─ /api/user                └─ (uses API via $fetch)           │
│                                                                  │
│  Tests run FROM Laravel → Browser visits Nuxt → API calls back  │
└─────────────────────────────────────────────────────────────────┘
```

## Running Tests

### Manual Server Start (Current)
```bash
# Terminal 1: Start Laravel API
php artisan serve --port=8000

# Terminal 2: Start Nuxt Frontend
cd ../pest-plugin-bridge-playground-nuxt && npm run dev

# Terminal 3: Run browser tests
./vendor/bin/pest tests/Browser
```

### Future: Automatic Server Management
The pest-plugin-bridge will eventually support:
```php
Bridge::setDefault('http://localhost:3000')
    ->serve('npm run dev', cwd: '../pest-plugin-bridge-playground-nuxt');
```

## Browser Testing Best Practices (Vue/Nuxt)

### Use `typeSlowly()` Instead of `fill()`

Vue's `v-model` doesn't sync with Playwright's `fill()` method because it sets the DOM value directly without triggering proper input events.

```php
// BAD - Vue v-model won't see the value
->fill('input#email', 'test@example.com')

// GOOD - Types character by character, triggers Vue reactivity
->typeSlowly('input#email', 'test@example.com', 20)
```

### Always `click()` First Input Before Typing

There's a timing issue where the first few characters get lost when typing immediately after page load.

```php
// BAD - First 3-4 characters may be lost
$this->bridge('/register')
    ->waitForEvent('networkidle')
    ->typeSlowly('input#name', 'TestUser', 30)  // Might become "User"

// GOOD - Click focuses and ensures readiness
$this->bridge('/register')
    ->waitForEvent('networkidle')
    ->click('input#name')
    ->typeSlowly('input#name', 'TestUser', 30)  // Full "TestUser"
```

### Use `waitForEvent('networkidle')` for API Calls

```php
->click('button[type="submit"]')
->waitForEvent('networkidle')  // Wait for API call to complete
->assertPathContains('/dashboard')
```

### DO NOT Use RefreshDatabase Trait

The `RefreshDatabase` trait wraps tests in a database transaction. This creates isolation that prevents seeing changes made by the external Laravel server.

```php
// tests/Pest.php

// BAD - External server writes won't be visible in test assertions
pest()->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Browser');

// GOOD - No transaction isolation
pest()->extends(TestCase::class)
    ->in('Browser');
```

### Verify Via UI, Not Database

Since database isolation doesn't work, verify results through UI assertions:

```php
// BAD - Database check won't see external server's writes
expect(User::where('email', $email)->exists())->toBeTrue();

// GOOD - Verify via UI
->assertPathContains('/dashboard')
->assertSee('Welcome')
```

### Use Unique Identifiers for Test Data

Without database refresh, use timestamps or unique IDs:

```php
$email = 'test'.time().'@example.com';
```

## Working Test Pattern

```php
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
```

## Laravel Configuration Notes

### Remove `statefulApi()` for Token-Based Auth

If using token-based API authentication (not session/cookie), remove the stateful middleware:

```php
// bootstrap/app.php

->withMiddleware(function (Middleware $middleware): void {
    // Token-based API auth doesn't need stateful middleware
    // $middleware->statefulApi();  // REMOVED - causes CSRF errors
})
```

### CORS Configuration

Ensure CORS allows the frontend origin:

```php
// config/cors.php
'allowed_origins' => ['http://localhost:3000'],
'supports_credentials' => true,
```

## Troubleshooting

### "CSRF token mismatch" Error
- Remove `statefulApi()` from `bootstrap/app.php`
- Or implement CSRF cookie fetching in frontend

### Form Values Not Submitting (Vue)
- Use `typeSlowly()` instead of `fill()`
- Check Vue DevTools to confirm reactive data is set

### First Characters Lost When Typing
- Add `->click('input#field')` before first `typeSlowly()`
- Increase the delay parameter (e.g., 30ms instead of 20ms)

### Database Assertions Fail But UI Shows Success
- Remove `RefreshDatabase` trait
- Use UI assertions instead of database queries

### Test Hangs/Timeouts
- Check if servers are running (Laravel port 8000, Nuxt port 3000)
- Use `waitForEvent('networkidle')` instead of `wait(5000)`

## pest-plugin-browser Method Reference

| Method | Works with Vue? | Notes |
|--------|----------------|-------|
| `fill($field, $value)` | No | Sets DOM but Vue v-model doesn't sync |
| `typeSlowly($field, $value, $delay)` | Yes | Triggers proper input events |
| `click($selector)` | Yes | Use before first typeSlowly |
| `press($buttonText)` | Yes | Clicks button by visible text |
| `waitForEvent($state)` | Yes | Use 'networkidle' for API calls |
| `assertSee($text)` | Yes | Waits for text to be visible |
| `assertPathContains($path)` | Yes | Checks current URL path |
| `assertValue($field, $value)` | Partial | Reads DOM value, not Vue state |

## Related Projects

- [pest-plugin-bridge](https://github.com/TestFlowLabs/pest-plugin-bridge) - The main plugin
- [pest-plugin-bridge-playground-nuxt](https://github.com/TestFlowLabs/pest-plugin-bridge-playground-nuxt) - Nuxt frontend
- [pestphp/pest-plugin-browser](https://github.com/pestphp/pest-plugin-browser) - Pest's browser testing
