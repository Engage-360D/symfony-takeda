<?php

namespace Engage360d\Bundle\TestBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $data = array(
            array('regular', false, array('ROLE_USER')),
            array('doctor', true, array('ROLE_USER', 'ROLE_DOCTOR')),
            array('admin', false, array('ROLE_USER', 'ROLE_ADMIN')),
        );

        foreach ($data as $row) {
            $user = $this->container
                ->get('engage360d_security.manager.user')
                ->createUser();

            $user->setFirstname($row[0]);
            $user->setLastname($row[0]);
            $user->setEmail($row[0] . '@example.com');
            $user->setPlainPassword($row[0]);
            $user->setBirthday(new \DateTime());
            $user->setEnabled(true);
            $user->setDoctor($row[1]);

            foreach ($row[2] as $role) {
                $user->addRole($role);
            }

            $manager->persist($user);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
