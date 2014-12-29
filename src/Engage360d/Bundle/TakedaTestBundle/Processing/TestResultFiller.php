<?php

namespace Engage360d\Bundle\TakedaTestBundle\Processing;

use Engage360d\Bundle\TakedaTestBundle\Entity\TestResult;

class TestResultFiller
{
    private $scoreCalculator;
    private $recommendationsMatcherFactory;

    public function __construct(ScoreCalculator $scoreCalculator, RecommendationsMatcherFactory $recommendationsMatcherFactory)
    {
        $this->scoreCalculator = $scoreCalculator;
        $this->recommendationsMatcherFactory = $recommendationsMatcherFactory;
    }

    public function fill(TestResult $testResult)
    {
        $score = $this->scoreCalculator->calculate($testResult);
        $testResult->setScore($score);

        $recommendations = $this->recommendationsMatcherFactory->factory($testResult)->match();
        $testResult->setRecommendations($recommendations);
    }
}
