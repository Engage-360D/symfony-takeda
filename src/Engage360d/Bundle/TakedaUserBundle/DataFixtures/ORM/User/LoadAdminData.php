<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\DataFixtures\ORM\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Melodia\UserBundle\Entity\User;

/**
 * Admin user.
 *
 * @author Andrey Linko <AndreyLinko@gmail.com>
 */

class LoadAdminData
    extends AbstractFixture
    implements OrderedFixtureInterface, ContainerAwareInterface
{
    public $container;

    public function load(ObjectManager $manager)
    {
        $user = new User();

        $user->setUsername('admin');
        $user->setPassword('12345Wq');
        $user->setFullName('Admin Admin');
        $user->setIsActive(true);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('user-admin', $user );
    }

    public function getOrder()
    {
        return 11;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
