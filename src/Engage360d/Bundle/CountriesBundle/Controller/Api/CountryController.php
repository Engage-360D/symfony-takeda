<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\CountriesBundle\Controller\Api;

use Symfony\Component\Intl\Intl;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Form\Exception\InvalidPropertyPathException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as Controller;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Engage360d\Bundle\RestBundle\Controller\RestController;

/**
 * Rest controller для работы со странами (countries).
 *
 * @author Andrey Linko <AndreyLinko@gmail.com>
 */
class CountryController extends RestController
{
    /**
     *
     *
     * @ApiDoc(
     *  resource=true,
     *  description="Получение списка стран.",
     *  filters={
     *      {"name"="limit", "dataType"="integer"},
     *      {"name"="page", "dataType"="integer"}
     *  }
     * )
     * 
     * @return Array Pages
     */
    public function getCountriesAction()
    {
        return Intl::getRegionBundle()->getCountryNames('ru');
    }
}
