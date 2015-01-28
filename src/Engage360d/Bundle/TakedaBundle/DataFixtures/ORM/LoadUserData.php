<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Admin user.
 *
 * @author Andrey Linko <AndreyLinko@gmail.com>
 */

class LoadUserData extends AbstractDataFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $users = $this->getData('users.yml');

        foreach ($users as $index => $userData) {
            $user = $this->container
                ->get('engage360d_security.manager.user')
                ->createUser();

            foreach ($userData as $methodName => $value) {
                $user->$methodName($value);
            }

            $user->setRegion($this->getReference('region_0'));

            $manager->persist($user);
            $manager->flush();

            $this->addReference('user_' . $index, $user);
        }
    }

    public function getOrder()
    {
        return 10;
    }
}
