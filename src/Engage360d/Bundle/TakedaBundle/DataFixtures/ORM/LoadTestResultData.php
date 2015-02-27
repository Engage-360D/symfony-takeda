<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\TestResult;

/**
 * Admin user.
 *
 * @author Andrey Linko <AndreyLinko@gmail.com>
 */

class LoadTestResultData extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $testResults = $this->getData('test_results.yml');

        foreach ($testResults as $index => $testResultData) {
            $result = new TestResult();

            foreach ($testResultData as $methodName => $value) {
                $result->$methodName($value);
            }

            $result->setUser($this->getReference('user_0'));

            $manager->persist($result);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 105;
    }
}
