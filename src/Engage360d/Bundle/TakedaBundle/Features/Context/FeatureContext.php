<?php

namespace Engage360d\Bundle\TakedaBundle\Features\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\TableNode;

class UriRetriever extends \JsonSchema\Uri\UriRetriever
{
    public function retrieve($uri, $baseUri = null)
    {
        $uri = str_replace(
            'https://cardiomagnyl.ru/api/v1/schemas',
            'file://' . __DIR__ . '/../../../../../../web/api/v1/schemas',
            $uri
        );

        return parent::retrieve($uri, $baseUri);
    }
}

function assertObjectValidBySchema($uri, $data) {
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
            json_encode($data, JSON_PRETTY_PRINT),
            $msg
        ));
    }

    \PHPUnit_Framework_Assert::assertTrue($validator->isValid(), $msg);
}

class FeatureContext implements SnippetAcceptingContext
{
    private $client;

    private $root;
    private $resource;
    private $requestBody;
    private $username;
    private $password;

    public function __construct($client)
    {
        $this->client = $client;
        $this->root = __DIR__ . '/../../../../../../web';
    }

    /**
     * @Given resource :resource
     */
    public function resource($resource)
    {
        $this->resource = $resource;
    }

    /**
     * @Given request body from example :example matched by schema :schema
     */
    public function requestBodyFromExampleMatchedBySchema($example, $schema)
    {
        $example = file_get_contents($this->root . $example);
        $exampleObject = json_decode($example);
        assertObjectValidBySchema('file://' . $this->root . $schema, $exampleObject);
        $this->requestBody = $example;
    }

    /**
     * @When I make :method request
     */
    public function iMakeRequest($method)
    {
        $parameters = array();
        $files = array();
        $server = array(
            'HTTP_CONTENT_TYPE' => 'application/vnd.api+json'
        );

        if ($this->username) {
            $server['PHP_AUTH_USER'] = $this->username;
        }

        if ($this->password) {
            $server['PHP_AUTH_PW'] = $this->password;
        }

        $this->client->request($method, $this->resource, $parameters, $files, $server, $this->requestBody);
    }

    /**
     * @Then response code should be :code
     */
    public function responseCodeShouldBe($code)
    {
        \PHPUnit_Framework_Assert::assertEquals(
            (int)$code,
            $this->client->getResponse()->getStatusCode(),
            $this->client->getResponse()->getContent()
        );
    }

    /**
     * @Then response body should match schema :schema
     */
    public function responseBodyShouldMatchSchema($schema)
    {
        $responseBody = json_decode($this->client->getResponse()->getContent());
        assertObjectValidBySchema('file://' . $this->root . $schema, $responseBody);
    }

    /**
     * @Then response data should be non empty array
     */
    public function responseDataShouldBeNonEmptyArray()
    {
        $responseBody = json_decode($this->client->getResponse()->getContent());
        \PHPUnit_Framework_Assert::assertTrue(is_array($responseBody->data));
        \PHPUnit_Framework_Assert::assertTrue(count($responseBody->data) > 0);
    }

    /**
     * @Given I am logined as :username with password :password
     */
    public function iAmLoginedAsWithPassword($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
}
