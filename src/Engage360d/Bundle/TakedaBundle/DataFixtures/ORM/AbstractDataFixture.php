<?php

namespace Engage360d\Bundle\TakedaBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Yaml\Parser;

abstract class AbstractDataFixture extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * The dependency injection container.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getEnvironment()
    {
        return $this->container->get('kernel')->getEnvironment();
    }

    public function getEnv()
    {
        return $this->getEnvironment();
    }

    public function getData($filename)
    {
        $path = __DIR__ . sprintf('/data/%s/%s', $this->getEnv(), $filename);

        if (!file_exists($path)) {
            return array();
        }

        $yaml = new Parser();
        $data = $yaml->parse(file_get_contents($path));

        return $data;
    }
}
