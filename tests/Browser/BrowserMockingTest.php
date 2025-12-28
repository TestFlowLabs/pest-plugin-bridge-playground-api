<?php

use TestFlowLabs\PestPluginBridge\BridgeTrait;

describe('BrowserMocking Frontend Mocking', function () {
    beforeEach(function () {
        BridgeTrait::clearBrowserMocks();
    });

    it('mocks direct frontend API calls', function () {
        BridgeTrait::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 200,
                'body' => [
                    'content' => 'Mocked inspirational quote for testing',
                    'author' => 'Mock Author',
                ],
            ],
        ]);

        $this->bridgeWithMocks('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('Mocked inspirational quote for testing')
            ->assertSee('Mock Author');
    });

    it('handles frontend API errors gracefully', function () {
        BridgeTrait::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 500,
                'body' => ['error' => 'Service unavailable'],
            ],
        ]);

        $this->bridgeWithMocks('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('Unable to fetch quote');
    });

    it('mocks specific quote content', function () {
        BridgeTrait::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 200,
                'body' => [
                    'content' => 'The only way to do great work is to love what you do',
                    'author' => 'Steve Jobs',
                ],
            ],
        ]);

        $this->bridgeWithMocks('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('The only way to do great work is to love what you do')
            ->assertSee('Steve Jobs');
    });
});
