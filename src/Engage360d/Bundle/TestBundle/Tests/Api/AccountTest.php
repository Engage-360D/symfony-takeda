<?php

namespace Engage360d\Bundle\TestBundle\Tests\Api;

use Engage360d\Bundle\TestBundle\Tests\ApiTestCase;

class AccountTest extends ApiTestCase
{
    public function testUpdateAccount()
    {
        $this->resource('PUT', '/api/v1/account')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/users/put.json',
                (object) array(
                    'users' => (object) array(
                      'firstname' => 'Test'
                    )
                )
            )
            ->setClient($this->getAnonymousClient())
                //->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/users/one.json')

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
                //->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(200);
    }
}
