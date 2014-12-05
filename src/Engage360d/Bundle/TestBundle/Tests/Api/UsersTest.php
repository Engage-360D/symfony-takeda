<?php

namespace Engage360d\Bundle\TestBundle\Tests\Api;

use Engage360d\Bundle\TestBundle\Tests\ApiTestCase;

class UsersTest extends ApiTestCase
{
    public function testGetUsers()
    {
        $this->resource('GET', '/api/v1/users')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/users/list.json');
    }

    public function testCreateUser()
    {
        $this->resource('POST', '/api/v1/users')
            ->setBody(
                'http://cardiomagnyl.ru/api/v1/schemas/users/post.json',
                (object) array(
                    'doctor' => false,
                    'email' => 'test@example.com',
                    'firstname' => 'vyacheslav',
                    'lastname' => 'slinko',
                    'region' => 'Москва',
                    'confirmSubscription' => true,
                    'plainPassword' => 'test',
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(201)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/users/one.json');
    }

    public function testGetUser()
    {
        $this->resource('GET', '/api/v1/users/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/users/one.json');
    }

    public function testUpdateUser()
    {
        $this->resource('PATCH', '/api/v1/users/1')
            ->setBody(
                'http://cardiomagnyl.ru/api/v1/schemas/users/patch.json',
                (object) array(
                    'confirmSubscription' => false
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('http://cardiomagnyl.ru/api/v1/schemas/users/one.json');
    }

    public function testDeleteUser()
    {
        $this->resource('DELETE', '/api/v1/users/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200);
    }

    public function testResetUserPassword()
    {
        $this->resource('POST', '/api/v1/users/1/reset-password')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200);
    }
}
