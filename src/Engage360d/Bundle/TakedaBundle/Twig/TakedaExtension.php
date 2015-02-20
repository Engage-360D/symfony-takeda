<?php

namespace Engage360d\Bundle\TakedaBundle\Twig;

class TakedaExtension extends \Twig_Extension
{
    private $geoIPResolver;

    public function __construct($geoIPResolver)
    {
        $this->geoIPResolver = $geoIPResolver;
    }

    public function getGlobals()
    {
        return [
            'geo_ip_city' => $this->geoIPResolver->getCityName()
        ];
    }

    public function getName()
    {
        return 'twig';
    }
}
