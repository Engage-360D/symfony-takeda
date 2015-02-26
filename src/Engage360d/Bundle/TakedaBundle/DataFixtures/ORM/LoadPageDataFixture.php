<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Melodia\PageBundle\Entity\Page;

class LoadPageDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('pages.yml');

        foreach ($data as $index => $item) {
            $page = new Page();

            foreach ($item as $methodName => $value) {
                if (method_exists($page, $methodName)) {
                    $page->$methodName($value);
                }
            }

            foreach ($item["pageBlocks"] as $keyword) {
                $page->addPageBlock($this->getReference('pageBlock_' . $keyword));
            }

            $manager->persist($page);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 104;
    }
}