<?php

namespace Engage360d\Bundle\TakedaBundle\Tests\Controller\Api;

use Engage360d\Bundle\TakedaBundle\Tests\ApiTestCase;

class PagesTest extends ApiTestCase
{
    public function testGetPages()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/pages')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/pages/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $items) {
                    $this->assertEquals(1, count($items));
                    $this->assertEquals('2', $items[0]->id);
                });
    }

    public function testCreatePage()
    {
        $this->markTestSkipped();return;
        $this->resource('POST', '/api/v1/pages')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/pages/post.json',
                (object) array(
                    "pages" => (object) array(
                        "url" => "/about",
                        "title" => "О компании",
                        "description" => "О компании",
                        "keywords" => "О компании",
                        "isActive" => true,
                        "links" => (object) array(
                            "pageBlocks" => ["1"]
                        )
                    )
                )
            )
                ->assertStatusCode(201)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/pages/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(400);
    }

    public function testGetPage()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/pages/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/pages/one.json');
    }

    public function testUpdatePage()
    {
        $this->markTestSkipped();return;
        $this->resource('PUT', '/api/v1/pages/1')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/pages/put.json',
                (object) array(
                    "pages" => (object) array(
                        "url" => "/about",
                        "title" => "О компании",
                        "description" => "О компании",
                        "keywords" => "О компании",
                        "isActive" => true,
                        "links" => (object) array(
                            "pageBlocks" => ["1"]
                        )
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
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/pages/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAdminClient())
                ->assertStatusCode(400);
    }

    public function testDeletePage()
    {
        $this->markTestSkipped();return;
        $this->resource('DELETE', '/api/v1/pages/4')
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
