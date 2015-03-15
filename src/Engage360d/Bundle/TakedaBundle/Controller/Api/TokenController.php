<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class TokenController extends TakedaJsonApiController
{
    const URI_TOKEN_ONE = 'v1/schemas/tokens/one.json';
    const URI_TOKEN_POST = 'v1/schemas/tokens/post.json';
    const URI_TOKEN_POST_FACEBOOK = 'v1/schemas/tokens/facebook/post.json';
    const URI_TOKEN_POST_VK = 'v1/schemas/tokens/vk/post.json';

    /**
     * @Route("/tokens", name="api_post_token", methods="POST")
     */
    public function tokensAction(Request $request)
    {
        $email = null;
        $plainPassword = null;

        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TOKEN_POST);

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
            ->getRepository('Engage360dTakedaBundle:User\User')
            ->findOneBy(array('email' => $email));

        if (!$user) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $encoder = $this->get('security.encoder_factory')->getEncoder($user);
        $password = $encoder->encodePassword($plainPassword, $user->getSalt());

        if ($user->getPassword() !== $password) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        // authenticate user
        $t = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->container->get("security.context")->setToken($t);

        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getTokenResource($token, $user), 201);
    }

    /**
     * Sign in through facebook.com
     * @Route("/tokens/facebook", name="api_post_token_facebook", methods="POST")
     */
    public function tokensFacebookAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TOKEN_POST_FACEBOOK);

        $accessToken = $data->data->access_token;

        // verify that token is valid
        $facebookId = $this->getFacebookId($accessToken);

        if (!$facebookId) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $user = $this->get('doctrine')
            ->getRepository('Engage360dTakedaBundle:User\User')
            ->findOneBy(array('facebookId' => $facebookId));

        if (!$user) {
            return $this->getErrorResponse("The user is not registered", 400);
        }

        // authenticate user
        $t = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->container->get("security.context")->setToken($t);

        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getTokenResource($token, $user), 201);
    }

    /**
     * Sign in through vk.com
     * @Route("/tokens/vk", name="api_post_token_vk", methods="POST")
     */
    public function tokensVkAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_TOKEN_POST_VK);

        $vkontakteId = $data->data->user_id;
        $accessToken = $data->data->access_token;

        // verify that token is valid
        if (!$this->isVkontakteCredentialsValid($vkontakteId, $accessToken)) {
            return $this->getErrorResponse("Bad credentials", 400);
        }

        $user = $this->get('doctrine')
            ->getRepository('Engage360dTakedaBundle:User\User')
            ->findOneBy(array('vkontakteId' => $vkontakteId));

        if (!$user) {
            return $this->getErrorResponse("The user is not registered", 400);
        }

        // authenticate user
        $t = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->container->get("security.context")->setToken($t);

        $token = $this->get('lexik_jwt_authentication.jwt_manager')->create($user);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getTokenResource($token, $user), 201);
    }
}
