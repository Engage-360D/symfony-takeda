<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use GoIntegro\Bundle\HateoasBundle\Controller\Controller;
use Engage360d\Bundle\TakedaUserBundle\Rest\Ghost\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use GoIntegro\Hateoas\JsonApi\DocumentPagination;

class TokenController extends Controller
{
    /**
     * @Route("/tokens", name="api_post_token", methods="POST")
     */
    public function tokensAction(Request $request)
    {
        $email = null;
        $plainPassword = null;

        // Handle json request
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (!isset($data['tokens']['email']) || !isset($data['tokens']['plainPassword'])) {
            return new JsonResponse('Bad credentials', 400);
        }
        $email = $data['tokens']['email'];
        $plainPassword = $data['tokens']['plainPassword'];

        // Handle HTTP basic authentication
        if (!$email) {
            $email = $request->headers->get('PHP_AUTH_USER');
        }
        if (!$plainPassword) {
            $plainPassword = $request->headers->get('PHP_AUTH_PW');
        }

        $user = $this->get('doctrine')
            ->getRepository('Engage360dTakedaUserBundle:User\User')
            ->findOneBy(array('email' => $email));

        if (!$user) {
            return new JsonResponse('Bad credentials', 400);
        }

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());

        if ($user->getPassword() !== $password) {
            return new JsonResponse('Bad credentials', 400);
        }

        $token = new Token($this->get('lexik_jwt_authentication.jwt_manager')->create($user));
        $token->setUser($user);

        $response = [
            "links" => [
                "tokens.user" => [
                    "href" => "https://cardiomagnyl.ru/api/v1/users/{tokens.user}",
                    "type" => "users"
                ]
            ],
            "tokens" => [
                "id" => $token->getId(),
                "links" => [
                    "user" => (String) $user->getId()
                ]
            ],
            "linked" => [
                "users" => [
                    "id" => (String) $user->getId(),
                    "email" => $user->getEmail(),
                    "firstname" => $user->getFirstname(),
                    "lastname" => $user->getLastname(),
                    "birthday" => $user->getBirthday()->format(\DateTime::ISO8601),
                    "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null,
                    "vkontakteId" => $user->getVkontakteId(),
                    "facebookId" => $user->getFacebookId(),
                    "specializationExperienceYears" => $user->getSpecializationExperienceYears(),
                    "specializationGraduationDate" => $user->getSpecializationGraduationDate(),
                    "specializationInstitutionAddress" => $user->getSpecializationInstitutionAddress(),
                    "specializationInstitutionName" => $user->getSpecializationInstitutionName(),
                    "specializationInstitutionPhone" => $user->getSpecializationInstitutionPhone(),
                    "specializationName" => $user->getSpecializationName(),
                    "roles" => $user->getRoles(),
                    "isEnabled" => $user->getEnabled(),
                ]
            ]
        ];

        return new JsonResponse($response, 201);

//         $resourceManager = $this->get('hateoas.resource_manager');
//         $resource = $resourceManager->createResourceFactory()
//             ->setEntity($token)
//             ->create();
//
//         $pagination = new DocumentPagination();
//
//         $json = $this->get('hateoas.serializer.factory')
//             ->setFields(['id'])
//             ->setInclude(['id'])
//             ->setPagination($pagination)
//             ->setDocumentResources($resource)
//             ->serialize();
//
//         return $this->createETagResponse($json);
    }
}
