<?php

namespace Engage360d\Bundle\TakedaBundle\Processing;

use Symfony\Component\Yaml\Parser as YamlParser;
use Symfony\Component\Routing\RouterInterface;
use Engage360d\Bundle\TakedaBundle\Entity\TestResult;

class RecommendationsMatcherFactory
{
    private $router;
    private $declaration;
  
    public function __construct(RouterInterface $router, $declarationFile)
    {
        $this->router = $router;

        $yamlParser = new YamlParser();
        $this->declaration = $yamlParser->parse(file_get_contents($declarationFile));
    }

    public function factory(TestResult $testResult)
    {
        return new RecommendationsMatcher($testResult, $this->declaration, $this->router);
    }
}
