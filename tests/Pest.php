<?php

use TestFlowLabs\PestPluginBridge\Bridge;
use Tests\TestCase;

// Database requirements for browser tests:
// - SQLite in-memory (:memory:) does NOT work - each connection gets isolated database
// - RefreshDatabase does NOT work - transaction isolation prevents API calls from seeing data
// - Use file-based SQLite or real database (configured in phpunit.xml)
// - Use DatabaseMigrations or DatabaseTruncation instead of RefreshDatabase
pest()->extends(TestCase::class)
    ->in('Browser');

// Configure external Nuxt frontend with automatic server management
// Note: readyWhen() is optional - the default pattern covers Nuxt, Vite, Next.js, CRA, Angular
Bridge::setDefault('http://localhost:3000')
    ->serve('npm run dev', cwd: '../pest-plugin-bridge-playground-nuxt');
