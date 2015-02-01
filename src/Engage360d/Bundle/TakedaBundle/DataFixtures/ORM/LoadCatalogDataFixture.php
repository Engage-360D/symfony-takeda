<?php

/**
 * This file is part of the Engage360d package bundles.
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Melodia\CatalogBundle\Entity\Catalog;
use Melodia\CatalogBundle\Entity\Record;

class LoadCatalogDataFixture extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $catalogs = $this->getData('catalogs.yml');

        foreach ($catalogs as $index => $data) {
            $catalog = new Catalog();

            foreach ($data as $methodName => $value) {
                if ($methodName === 'records') {
                    $this->setCatalogRecords($catalog, $value, $manager);
                } else {
                    $catalog->$methodName($value);
                }
            }

            $manager->persist($catalog);

            $this->addReference('catalog_' . $catalog->getId(), $catalog);
        }

        $manager->flush();
    }

    private function setCatalogRecords(&$catalog, &$records, $manager)
    {
        foreach ($records as $index => $data) {
            $record = new Record();
            foreach ($data as $methodName => $value) {
                $record->$methodName($value);
            }
            $record->setCatalog($catalog);
            $catalog->addRecord($record);

            $manager->persist($record);
            $manager->flush();

            $this->addReference($catalog->getId() .'_record_' . $index, $record);
        }
    }

    public function getOrder()
    {
        return 2;
    }
}

