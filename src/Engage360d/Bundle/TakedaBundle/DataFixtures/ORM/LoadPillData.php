<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\Pill;

class LoadPillData extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $pills = $this->getData('pills.yml');

        foreach ($pills as $index => $data) {
            $pill = new Pill();

            foreach ($data as $methodName => $value) {
                $pill->$methodName($value);
            }
            $pill->setUser($this->getReference('user_0'));

            $manager->persist($pill);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 106;
    }
}

