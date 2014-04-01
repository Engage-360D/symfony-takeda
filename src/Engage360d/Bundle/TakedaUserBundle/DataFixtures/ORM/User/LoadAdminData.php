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
        $user = $this->container
          ->get('engage360d_security.manager.user')
          ->createUser();

        $user->setUsername('admin');
        $user->setFirstname('admin');
        $user->setLastname('admin');
        $user->setEmail('test@test.ru');
        $user->setPlainPassword('password');
        $user->setBirthday(new \DateTime());
        $user->setEnabled(true);
        $user->addRole("ROLE_ADMIN");

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
