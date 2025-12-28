<?php

use TestFlowLabs\PestPluginBridge\Bridge;

describe('Bridge::fake() Backend Mocking', function () {
    it('mocks external API calls from Laravel backend', function () {
        Bridge::fake([
            'https://ipapi.co/*' => [
                'status' => 200,
                'body' => [
                    'country_name' => 'Mocked Country',
                    'city' => 'Mocked City',
                    'region' => 'Mocked Region',
                    'ip' => '1.2.3.4',
                ],
            ],
        ]);

        $this->bridge('/ip-info')
            ->waitForEvent('networkidle')
            ->assertSee('Mocked Country')
            ->assertSee('Mocked City')
            ->assertSee('Mocked Region')
            ->assertSee('1.2.3.4');
    });

    it('handles external API errors gracefully', function () {
        Bridge::fake([
            'https://ipapi.co/*' => [
                'status' => 500,
                'body' => ['error' => 'Service unavailable'],
            ],
        ]);

        $this->bridge('/ip-info')
            ->waitForEvent('networkidle')
            ->assertSee('Unable to fetch location');
    });

    it('mocks specific country data for testing', function () {
        Bridge::fake([
            'https://ipapi.co/*' => [
                'status' => 200,
                'body' => [
                    'country_name' => 'Turkey',
                    'city' => 'Istanbul',
                    'region' => 'Istanbul',
                    'ip' => '88.88.88.88',
                ],
            ],
        ]);

        $this->bridge('/ip-info')
            ->waitForEvent('networkidle')
            ->assertSee('Turkey')
            ->assertSee('Istanbul');
    });
});
