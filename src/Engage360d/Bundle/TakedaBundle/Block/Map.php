<?php

namespace Engage360d\Bundle\TakedaBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class Map extends BaseBlockService
{
    private static $blockId = 0;

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'Engage360dTakedaBundle:Block:map.html.twig',
            'map' => array(),
            'placemarks' => array(),
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $blockSettings = $blockContext->getSettings();

        return $this->renderResponse($blockContext->getTemplate(), array(
            'map' => $blockSettings['map'],
            'placemarks' => $blockSettings['placemarks'],
            'blockId' => self::$blockId++
        ), $response);
    }
}
