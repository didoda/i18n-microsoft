<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\I18n\Microsoft\Test\Core;

use BEdita\I18n\Microsoft\Core\TranslateClient;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\I18n\Microsoft\Core\TranslateClient} Test Case
 *
 * @covers \BEdita\I18n\Microsoft\Core\TranslateClient
 */
class TranslateClientTest extends TestCase
{
    /**
     * Test constructor.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testConstructor(): void
    {
        $client = new class (['auth_key' => 'test-auth-key']) extends TranslateClient
        {
            /**
             * Get headers
             *
             * @return array
             */
            public function getHeaders(): array
            {
                return $this->headers;
            }
        };
        $expected = [
            'Content-type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => 'test-auth-key',
        ];
        $actual = $client->getHeaders();
        static::assertSame($expected, $actual);
    }

    /**
     * Test `translate` method.
     *
     * @return void
     * @covers ::translate()
     */
    public function testTranslate(): void
    {
        $client = new class (['auth_key' => 'test-auth-key']) extends TranslateClient
        {
            /**
             * Get headers
             *
             * @return array
             */
            public function getHeaders(): array
            {
                return $this->headers;
            }

            /**
             * @inheritDoc
             */
            public function apiCall(string $from, string $to, array $options): string
            {
                $content = $options['http']['content'];
                $text = json_decode($content, true)[0]['Text'];
                $text = sprintf('translation of "%s" from %s to %s', $text, $from, $to);

                return json_encode([
                    [
                        'translations' => [
                            [$text],
                        ],
                    ],
                ]);
            }
        };
        $expected = '[{"translations":[["translation of \"Hello world!\" from en to it"]]}]';
        $actual = $client->translate('Hello world!', 'en', 'it');
        static::assertSame($expected, $actual);
    }

    /**
     * Test `apiCall` method.
     *
     * @return void
     * @covers ::apiCall()
     */
    public function testApiCall(): void
    {
        static::markTestIncomplete('Not implemented yet');
    }
}
