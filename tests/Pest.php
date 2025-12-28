<?php

use TestFlowLabs\PestPluginBridge\Bridge;
use Tests\TestCase;

// Note: RefreshDatabase doesn't work with external server browser tests
// The transaction isolation prevents seeing changes made by the external server
pest()->extends(TestCase::class)
    ->in('Browser');

// Configure external Nuxt frontend with automatic server management
Bridge::setDefault('http://localhost:3000')
    ->serve('npm run dev', cwd: '../pest-plugin-bridge-playground-nuxt')
    ->readyWhen('Local:.*http');
