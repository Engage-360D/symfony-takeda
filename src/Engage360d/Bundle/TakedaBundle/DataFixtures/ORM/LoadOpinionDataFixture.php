<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Opinion;

class LoadOpinionDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('opinions.yml');

        foreach ($data as $index => $item) {
            $opinion = new Opinion();

            foreach ($item as $methodName => $value) {
                $opinion->$methodName($value);
            }
            $opinion->setExpert($this->getReference('expert_' . ($index % 2)));

            $manager->persist($opinion);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 102;
    }
}

