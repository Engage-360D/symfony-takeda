<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Engage360d\Bundle\TakedaUserBundle\Rest\Ghost\Token;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;

class TokenController extends JsonApiController
{
    const URI_TOKEN_ONE = '/api/v1/schemas/tokens/one.json';
    const URI_TOKEN_POST = '/api/v1/schemas/tokens/post.json';
    const URI_TOKEN_POST_FACEBOOK = '/api/v1/schemas/tokens/facebook/post.json';
    const URI_TOKEN_POST_VK = '/api/v1/schemas/tokens/vk/post.json';

    protected function getTokenResource($token, $user)
    {
        return [
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
    }

    /**
     * @Route("/tokens", name="api_post_token", methods="POST")
     */
    public function tokensAction(Request $request)
    {
        $email = null;
        $plainPassword = null;

        if (!$this->isContentTypeValid($request)) {
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
        }

        // Handle json request
        $body = $request->getContent();
        $data = json_decode($body);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TOKEN_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
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
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());

        if ($user->getPassword() !== $password) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $token = new Token($this->get('lexik_jwt_authentication.jwt_manager')->create($user));
        $token->setUser($user);

        return new JsonResponse($this->getTokenResource($token, $user), 201);
    }

    /**
     * Sign in through facebook.com
     * @Route("/tokens/facebook", name="api_post_token_facebook", methods="POST")
     */
    public function tokensFacebookAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
        }

        // Handle json request
        $body = $request->getContent();
        $data = json_decode($body);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TOKEN_POST_FACEBOOK
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $access_token = $data->data->access_token;

        // verify that token is valid
        $buzz = $this->container->get('buzz');
        $response = $buzz->get('https://graph.facebook.com/v2.2/me?access_token=' . urlencode($access_token));

        if (!$response->isSuccessful()) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $body = $response->getContent();
        $data = json_decode($body);

        $facebookId = isset($data->id) ? $data->id : null;

        $user = $this->get('doctrine')
            ->getRepository('Engage360dTakedaUserBundle:User\User')
            ->findOneBy(array('facebookId' => $facebookId));

        if (!$user) {
            return $this->getErrorResponse("The user is not registered", 400);
        }

        $token = new Token($this->get('lexik_jwt_authentication.jwt_manager')->create($user));
        $token->setUser($user);

        return new JsonResponse($this->getTokenResource($token, $user), 201);
    }

    /**
     * Sign in through vk.com
     * @Route("/tokens/vk", name="api_post_token_vk", methods="POST")
     */
    public function tokensVkAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getErrorResponse("The expected content type is \"application/vnd.api+json\"", 400);
        }

        // Handle json request
        $body = $request->getContent();
        $data = json_decode($body);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_TOKEN_POST_VK
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $access_token = $data->data->access_token;
        $vkontakteId = $data->data->user_id;

        // verify that token is valid
        $buzz = $this->container->get('buzz');
        $response = $buzz->get(sprintf(
            'https://api.vk.com/method/users.isAppUser?v=5.27&user_id=%s&access_token=%s',
            $vkontakteId,
            urlencode($access_token)
        ));

        if (!$response->isSuccessful()) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $body = $response->getContent();
        $data = json_decode($body);

        if (!isset($data->response) || $data->response !== 1) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $user = $this->get('doctrine')
            ->getRepository('Engage360dTakedaUserBundle:User\User')
            ->findOneBy(array('vkontakteId' => $vkontakteId));

        if (!$user) {
            return $this->getErrorResponse("The user is not registered", 400);
        }

        $token = new Token($this->get('lexik_jwt_authentication.jwt_manager')->create($user));
        $token->setUser($user);

        return new JsonResponse($this->getTokenResource($token, $user), 201);
    }



}
