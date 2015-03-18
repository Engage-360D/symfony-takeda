<?php

namespace Engage360d\Bundle\TakedaBundle\Block;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class GoodToKnow extends BaseBlockService
{
    private static $blockId = 1;

    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'Engage360dTakedaBundle:Block:good_to_know.html.twig',
            'color' => '',
            'image' => '',
            'title' => '',
            'content' => '',
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $blockSettings = $blockContext->getSettings();

        return $this->renderResponse($blockContext->getTemplate(), array(
            'color' => $blockSettings['color'],
            'image' => $blockSettings['image'],
            'title' => $blockSettings['title'],
            'content' => $blockSettings['content'],
            'blockId' => self::$blockId++
        ), $response);
    }
}
