<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert;

class LoadExpertDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('experts.yml');

        foreach ($data as $index => $item) {
            $expert = new Expert();

            foreach ($item as $methodName => $value) {
                $expert->$methodName($value);
            }

            $manager->persist($expert);

            $this->addReference('expert_' . $index, $expert);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 101;
    }
}

