<?php

namespace Engage360d\Bundle\TakedaBundle;

require(__DIR__ . '/ipgeobase.php/ipgeobase.php');

class GeoIPResolver
{
    private $geoBase;
    private $requestStack;

    public function __construct($requestStack)
    {
        $this->geoBase = new \IPGeoBase();
        $this->requestStack = $requestStack;
    }

    public function getCityName()
    {
        $ip = $this->requestStack->getCurrentRequest()->getClientIp();
        $data = $this->geoBase->getRecord($ip);
        return $data ? iconv('CP1251', 'UTF-8', $data['city']) : 'Москва';
    }
}
