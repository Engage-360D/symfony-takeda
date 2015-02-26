<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Melodia\PageBundle\Entity\PageBlock;

class LoadPageBlockDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $data = $this->getData('page_blocks.yml');

        foreach ($data as $index => $item) {
            $pageBlock = new PageBlock();

            foreach ($item as $methodName => $value) {
                if (method_exists($pageBlock, $methodName)) {
                    $pageBlock->$methodName($value);
                }
            }
            $pageBlock->setJson(json_encode($item['json']));

            $manager->persist($pageBlock);

            $this->addReference('pageBlock_' . $pageBlock->getKeyword(), $pageBlock);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 103;
    }
}