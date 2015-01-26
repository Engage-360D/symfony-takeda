<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;

class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = array(
            'Москва',
            'Санкт-Петербург',
        );

        foreach ($data as $index => $name) {
            $region = new Region();
            $region->setName($name);

            $manager->persist($region);

            $this->addReference('region_' . $index, $region);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 10;
    }
}

