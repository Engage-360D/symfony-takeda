<?php

namespace Engage360d\Bundle\TakedaTestBundle\Controller;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Engage360d\Bundle\RestBundle\Controller\RestController;
use Engage360d\Bundle\TakedaTestBundle\Form\Type\TestResultType;

class ApiController extends RestController
{
    /**
     * @Post("/test-results/")
     * @ApiDoc(
     *  resource=true,
     *  description="Сохранение результатов тестирования.",
     *  input="Engage360d\Bundle\TakedaTestBundle\Form\Type\TestResultType",
     *  output="Engage360d\Bundle\TakedaTestBundle\Entity\TestResult"
     * )
     * @return TestResult
     */
    public function postTestResultsAction()
    {
        // $user = $this->container->get('security.context')->getToken()->getUser();

        // if (!$user instanceof UserInterface) {
        //     return new Response(null, 401);
        // }

        $form = $this->createForm(new TestResultType());
        $form->submit($this->getRequest());

        if (!$form->isValid()) {
            return new JsonResponse($this->getErrorMessages($form), 400);
        }
        
        // if (!$user->isDoctor() && count($user->getTestResults()) > 0) {
        //     return new JsonResponse(array("_form" => array("Test already passed by user.")), 400);
        // }

        $testResult = $form->getData();
        // $testResult->setUser($user);
        
        $this->getDoctrine()->getEntityManager()->persist($testResult);
        $this->getDoctrine()->getEntityManager()->flush();

        return $testResult;
    }
}
