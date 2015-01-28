<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;

class LoadRegionData extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $regions = $this->getData('regions.yml');

        foreach ($regions as $index => $data) {
            $region = new Region();

            foreach ($data as $methodName => $value) {
                $region->$methodName($value);
            }

            $manager->persist($region);

            $this->addReference('region_' . $index, $region);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}

