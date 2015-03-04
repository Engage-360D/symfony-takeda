<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\Institution;

class LoadInstitutionDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('institutions.yml');

        foreach ($data as $index => $item) {
            $institution = new Institution();

            foreach ($item as $methodName => $value) {
                $institution->$methodName($value);
            }

            $manager->persist($institution);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 101;
    }
}

