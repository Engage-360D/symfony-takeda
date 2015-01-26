<?php

namespace Engage360d\Bundle\TakedaBundle\Tests\Controller\Api;

use Engage360d\Bundle\TakedaBundle\Tests\ApiTestCase;

class TestResultsTest extends ApiTestCase
{
    public function testGetUserTestResults()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/account/test-results')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/test-results/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $items) {
                    $this->assertEquals(1, count($items));
                    $this->assertEquals('2', $items[0]->id);
                });
    }

    public function testGetAllTestResults()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/test-results')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/test-results/list.json')

            ->setQuery(array('page' => 2, 'limit' => 1))
            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->testResponse(function($response, $items) {
                    $this->assertEquals(1, count($items));
                    $this->assertEquals('2', $items[0]->id);
                });
    }

    public function testCreateTestResult()
    {
        $this->markTestSkipped();return;
        $this->resource('POST', '/api/v1/account/test-results')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/test-results/post.json',
                (object) array(
                    "testResults" => (object) array(
                        "sex" => "male",
                        "birthday" => "1991-01-19T00:00:00+0000",
                        "growth" => 185,
                        "weight" => 98,
                        "isSmoker" => false,
                        "cholesterolLevel" => null,
                        "isCholesterolDrugsConsumer" => null,
                        "hasDiabetes" => false,
                        "hadSugarProblems" => false,
                        "isSugarDrugsConsumer" => null,
                        "arterialPressure" => 120,
                        "isArterialPressureDrugsConsumer" => null,
                        "physicalActivityMinutes" => 100,
                        "hadHeartAttackOrStroke" => true,
                        "isAddingExtraSalt" => true,
                        "isAcetylsalicylicDrugsConsumer" => true
                    )
                )
            )
                ->assertStatusCode(201)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/test-results/one.json')

            ->setClient($this->getRegularUserClient())
            ->setBody(
                null,
                (object) array()
            )
                ->assertStatusCode(400);
    }

    public function testGetTestResult()
    {
        $this->markTestSkipped();return;
        $this->resource('GET', '/api/v1/test-results/1')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
                ->assertStatusCode(403)

            ->setClient($this->getDoctorClient())
                ->assertStatusCode(403)

            ->setClient($this->getAdminClient())
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/test-results/one.json');
    }

    public function testSendEmail()
    {
        $this->markTestSkipped();return;
        // TODO: пользователь может отправлять только свои результаты
        $this->resource('POST', '/api/v1/test-results/1/send-email')
            ->setClient($this->getAnonymousClient())
                ->assertStatusCode(401)

            ->setClient($this->getRegularUserClient())
            ->setBody(
                'https://cardiomagnyl.ru/api/v1/schemas/test-results/send-email/post.json',
                (object) array(
                    "sendEmail" => (object) array(
                        "email" => "vslinko@yahoo.com"
                    )
                )
            )
                ->assertStatusCode(200)
                ->assertResponseBySchema('https://cardiomagnyl.ru/api/v1/schemas/test-results/send-email/one.json');
    }
}
