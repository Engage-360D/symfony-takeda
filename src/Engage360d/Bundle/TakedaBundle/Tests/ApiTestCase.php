<?php

namespace Engage360d\Bundle\TakedaBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;

class UriRetriever extends \JsonSchema\Uri\UriRetriever
{
    public function retrieve($uri, $baseUri = null)
    {
        $uri = str_replace(
            'https://cardiomagnyl.ru/api/v1/schemas',
            'file://' . __DIR__ . '/../../../../../web/api/v1/schemas',
            $uri
        );

        return parent::retrieve($uri, $baseUri);
    }
}

class ApiTest
{
    private $testCase;
    private $client;
    private $method;
    private $url;
    private $query;
    private $body;
    private $requested = false;

    public function __construct($testCase)
    {
        $this->testCase = $testCase;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        $this->requested = false;
        return $this;
    }

    public function setUrl($url)
    {
        $this->url = $url;
        $this->requested = false;
        return $this;
    }

    public function setQuery($query)
    {
        $this->query = $query;
        $this->requested = false;
        return $this;
    }

    public function setBody($uri, $body)
    {
        if ($uri) {
            $this->assertBySchema($uri, $body);
        }

        $this->body = $body;
        $this->requested = false;
        return $this;
    }

    public function setClient($client)
    {
        $this->client = $client;
        $this->requested = false;
        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function testResponse($callback)
    {
        $response = $this->client->getResponse();
        $body = json_decode($response->getContent());

        $callback(
            $response,
            $body
        );
    }

    public function assertStatusCode($code)
    {
        $this->shouldMakeRequest();

        $this->testCase->assertEquals(
            $code,
            $this->client->getResponse()->getStatusCode()
        );

        return $this;
    }

    public function assertResponseBySchema($uri)
    {
        $this->shouldMakeRequest();

        $this->assertBySchema(
            $uri,
            json_decode($this->client->getResponse()->getContent())
        );

        return $this;
    }

    protected function assertBySchema($uri, $data)
    {
        $retriever = new UriRetriever();
        $schema = $retriever->retrieve($uri);

        $refResolver = new \JsonSchema\RefResolver($retriever);
        $refResolver->resolve($schema);

        $validator = new \JsonSchema\Validator();
        $validator->check($data, $schema);

        $msg = '';
        if (!$validator->isValid()) {
            $msg = implode("\n", array_map(function($error) {
                return sprintf("    [%s] %s", $error['property'], $error['message']);
            }, $validator->getErrors()));

            $msg = implode("\n", array(
                sprintf("Failed asserting by schema %s", $uri),
                $msg
            ));
        }

        $this->testCase->assertTrue($validator->isValid(), $msg);
    }

    protected function shouldMakeRequest()
    {
        if (!$this->requested) {
            $this->makeRequest();
            $this->requested = true;
        }
    }

    protected function makeRequest()
    {
        $method = $this->method;
        $url = $this->url;
        $parameters = array();
        $files = array();
        $server = array();
        $content = null;

        if ($this->query) {
            $url = implode("?", array(
                $url,
                http_build_query($this->query)
            ));
        }

        if ($this->body) {
            $server['CONTENT_TYPE'] = 'application/vnd.api+json';
            $content = json_encode($this->body);
        }

        $this->client->request(
            $method,
            $url,
            $parameters,
            $files,
            $server,
            $content
        );

        return $this;
    }
}

class ApiTestCase extends WebTestCase
{
    protected function resource($method, $url)
    {
        $test = new ApiTest($this);
        $test->setMethod($method);
        $test->setUrl($url);

        return $test;
    }

    protected function getAnonymousClient()
    {
        return static::createClient();
    }

    protected function getRegularUserClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'regular@example.com',
            'PHP_AUTH_PW' => 'regular',
        ));
    }

    protected function getDoctorClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'doctor@example.com',
            'PHP_AUTH_PW' => 'doctor',
        ));
    }

    protected function getAdminClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin@example.com',
            'PHP_AUTH_PW' => 'admin',
        ));
    }
}
