<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use GoIntegro\Bundle\HateoasBundle\Controller\Controller;
use Engage360d\Bundle\TakedaUserBundle\Rest\Ghost\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;

class TokenController extends Controller
{
    /**
     * @Route("/tokens", name="api_post_token", methods="POST")
     */
    public function tokensAction(Request $request)
    {
        $email = null;
        $plainPassword = null;

        // TODO extract it into a function isContentTypeValid()
        if (!empty($request->request) && $request->headers->get('content-type') !== 'application/vnd.api+json') {
            return new JsonResponse(
                ["errors" => [
                    "code" => 400,
                    "message" => "The expected content type is \"application/vnd.api+json\""
                ]],
                400
            );
        }

        // Handle json request
        $body = $request->getContent();
        $data = json_decode($body);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            '/api/v1/schemas/tokens/post.json'
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            // TODO extract it into a function getErrorResponse(string:message|array, int:code)
            // which should return JsonResponse
            // The first argument may be error message or array of errors
            $errors  = [];
            foreach($validator->getErrors() as $error) {
                $errors[] = [
                    "code" => 400,
                    "message" => $error['message'],
                ];
            }

            return new JsonResponse(["errors" => $errors], 400);
        }

        $email = $data->data->email;
        $plainPassword = $data->data->plainPassword;

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
            "data" => [
                "id" => $token->getId(),
                "links" => [
                    "user" => (String) $user->getId()
                ]
            ],
            "linked" => [
                "users" => [
                    [
                      "id" => (String) $user->getId(),
                      "email" => $user->getEmail(),
                      "firstname" => $user->getFirstname(),
                      "lastname" => $user->getLastname(),
                      "birthday" => $user->getBirthday()->format(\DateTime::ISO8601),
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
                      "links" => [
                          "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null,
                      ]
                    ]
                ]
            ]
        ];

        return new JsonResponse($response, 201);
    }
}
