<?php

namespace Engage360d\Bundle\TakedaBundle\Tests\Controller\Api;

use Engage360d\Bundle\TakedaBundle\Tests\ApiTestCase;

class RegionsTest extends ApiTestCase
{
    public function testGetRegions()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/regions')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/regions/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $regions) {
                    $this->assertEquals(1, count($regions));
                    $this->assertEquals('2', $regions[0]->id);
                });
    }

    public function testGetRegion()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/regions/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/regions/one.json');
    }
}
