<?php

namespace Engage360d\Bundle\TestBundle\Tests\Api;

use Engage360d\Bundle\TestBundle\Tests\ApiTestCase;

class TokensTest extends ApiTestCase
{
    public function testCreateToken()
    {
        $this->resource('POST', '/api/v1/tokens')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/tokens/post.json',
                (object) array(
                    'tokens' => (object) array(
                        'email' => 'regular@example.com',
                        'plainPassword' => 'regular'
                    )
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(201)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/tokens/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(400);
    }
}
