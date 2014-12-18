<?php

namespace Engage360d\Bundle\TestBundle\Tests\Api;

use Engage360d\Bundle\TestBundle\Tests\ApiTestCase;

class PageBlocksTest extends ApiTestCase
{
    public function testGetPageBlocks()
    {
        $this->resource('GET', '/api/v1/page-blocks')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/page-blocks/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $items) {
                    $this->assertEquals(1, count($items));
                    $this->assertEquals('2', $items[0]->id);
                });
    }

    public function testCreatePageBlock()
    {
        $this->resource('POST', '/api/v1/page-blocks')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/page-blocks/post.json',
                (object) array(
                    "pageBlocks" => (object) array(
                        "type" => "text",
                        "json" => "{}"
                    )
                )
            )
                ->assertStatusCode(201)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/page-blocks/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(400);
    }

    public function testGetPageBlock()
    {
        $this->resource('GET', '/api/v1/page-blocks/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/page-blocks/one.json');
    }

    public function testUpdatePageBlock()
    {
        $this->resource('PUT', '/api/v1/page-blocks/1')
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/page-blocks/put.json',
                (object) array(
                    'pageBlocks' => (object) array(
                        'json' => '{}'
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
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/page-blocks/one.json')

            ->setBody(
                null,
                (object) array()
            )
            ->setClient($this->getAdminClient())
                ->assertStatusCode(400);
    }

    public function testDeletePageBlock()
    {
        $this->resource('DELETE', '/api/v1/page-blocks/4')
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
