<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use TestFlowLabs\PestPluginBridge\Bridge;
use Tests\TestCase;

pest()->extends(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Browser');

Bridge::setDefault('http://localhost:3000')
    ->serve('npm run dev', cwd: __DIR__.'/../../../pest-plugin-bridge-playground-nuxt');
