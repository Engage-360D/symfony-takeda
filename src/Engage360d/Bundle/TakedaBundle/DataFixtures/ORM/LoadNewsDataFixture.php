<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaBundle\Entity\PressCenter\News;

class LoadNewsDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('news.yml');

        foreach ($data as $index => $item) {
            $news = new News();

            foreach ($item as $methodName => $value) {
                $news->$methodName($value);
            }
            $news->setCategory($this->getReference('NewsCategories_record_' . ($index % 2)));

            $manager->persist($news);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 100;
    }
}

