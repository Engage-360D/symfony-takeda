<?php

namespace Engage360d\Bundle\TestBundle\Tests\Api;

use Engage360d\Bundle\TestBundle\Tests\ApiTestCase;

class AccountTest extends ApiTestCase
{
    public function testCreateAccount()
    {
        $this->resource('POST', '/api/v1/account')
            ->setBody(
                'http://cardiomagnyl.ru/api/v1/schemas/account/post.json',
                (object) array(
                    'email' => 'regular@example.com',
                    'plainPassword' => 'regular',
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(201)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/account/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(400);
    }

    public function testUpdateAccount()
    {
        $this->resource('PATCH', '/api/v1/account')
            ->setBody(
                'http://cardiomagnyl.ru/api/v1/schemas/users/patch.json',
                (object) array(
                    'confirmSubscription' => false
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/users/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAdminClient())
                ->assertStatusCode(400);
    }

    public function testResetAccountPassword()
    {
        $this->resource('POST', '/api/v1/account/reset-password')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(200);
    }
}
