<?php

namespace Engage360d\Bundle\TakedaBundle\Tests\Controller\Api;

use Engage360d\Bundle\TakedaBundle\Tests\ApiTestCase;

class UsersTest extends ApiTestCase
{
    public function testGetUsers()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/users')
            ->setClient($this->getAnonymousClient())
 //               ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
//                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
//                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/users/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $users) {
                    $this->assertEquals(1, count($users));
                    $this->assertEquals('2', $users[0]->id);
                });
    }

    public function testCreateUser()
    {
        $this->markTestSkipped();return;
        $this->resource('POST', '/api/v1/users')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/users/post.json',
                (object) array(
                    'users' => (object) array(
                        'email' => 'vslinko@yahoo.com',
                        'firstname' => 'Vyacheslav',
                        'lastname' => 'Slinko',
                        'birthday' => '1991-01-19T00:00:00+0000',
                        'specializationExperienceYears' => null,
                        'specializationGraduationDate' => null,
                        'specializationInstitutionAddress' => null,
                        'specializationInstitutionName' => null,
                        'specializationInstitutionPhone' => null,
                        'specializationName' => null,
                        'plainPassword' => 'password',
                        'isDoctor' => false,
                        'isSubscribed' => true,
                        'links' => (object) array(
                          'region' => '1'
                        )
                    )
                )
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(201)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/users/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(400);
    }

    public function testGetUser()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/users/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/users/one.json');
    }

    public function testUpdateUser()
    {
        $this->markTestSkipped();return;
        $this->resource('PUT', '/api/v1/users/1')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/users/put.json',
                (object) array(
                    'users' => (object) array(
                      'firstname' => 'Test'
                    )
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
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/users/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAdminClient())
                ->assertStatusCode(400);
    }

    public function testDeleteUser()
    {
        $this->markTestSkipped();return;
        $this->resource('DELETE', '/api/v1/users/4')
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
        $this->markTestSkipped();return;
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
