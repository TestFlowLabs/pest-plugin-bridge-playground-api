<?php

use TestFlowLabs\PestPluginBridge\Bridge;

describe('Bridge::mockBrowser() Frontend Mocking', function () {
    beforeEach(function () {
        Bridge::clearBrowserMocks();
    });

    it('mocks direct frontend API calls', function () {
        Bridge::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 200,
                'body' => [
                    'content' => 'Mocked inspirational quote for testing',
                    'author' => 'Mock Author',
                ],
            ],
        ]);

        $this->bridge('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('Mocked inspirational quote for testing')
            ->assertSee('Mock Author');
    });

    it('handles frontend API errors gracefully', function () {
        Bridge::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 500,
                'body' => ['error' => 'Service unavailable'],
            ],
        ]);

        $this->bridge('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('Unable to fetch quote');
    });

    it('mocks specific quote content', function () {
        Bridge::mockBrowser([
            'https://api.quotable.io/*' => [
                'status' => 200,
                'body' => [
                    'content' => 'The only way to do great work is to love what you do',
                    'author' => 'Steve Jobs',
                ],
            ],
        ]);

        $this->bridge('/quote')
            ->waitForEvent('networkidle')
            ->assertSee('The only way to do great work is to love what you do')
            ->assertSee('Steve Jobs');
    });
});
