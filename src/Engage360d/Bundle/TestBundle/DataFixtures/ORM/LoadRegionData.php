<?php

namespace Engage360d\Bundle\TestBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Engage360d\Bundle\TakedaUserBundle\Entity\Region\Region;

class LoadRegionData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = array(
            array('Moscow'),
        );

        foreach ($data as $index => $row) {
            $region = new Region();
            $region->setName($row[0]);

            $this->addReference('region-' . $index, $region);
            $manager->persist($region);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
