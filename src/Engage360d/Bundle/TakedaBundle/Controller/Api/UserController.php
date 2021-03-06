<?php

namespace Engage360d\Bundle\TakedaBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Engage360d\Bundle\TakedaBundle\Controller\TakedaJsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;
use Engage360d\Bundle\TakedaBundle\Entity\Region\Region;
use Engage360d\Bundle\SecurityBundle\Event\UserEvent;
use Engage360d\Bundle\SecurityBundle\Engage360dSecurityEvents;

class UserController extends TakedaJsonApiController
{
    const URI_USERS_LIST = 'v1/schemas/users/list.json';
    const URI_USERS_ONE =  'v1/schemas/users/one.json';
    const URI_USERS_POST = 'v1/schemas/users/post.json';
    const URI_USERS_PUT =  'v1/schemas/users/put.json';

    /**
     * @Route("/users", name="api_get_users", methods="GET")
     */
    public function getUsersAction(Request $request)
    {
        // access control lets here only authorized users
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        $this->assertContentTypeIsValid($request);

        $limit = $request->query->get("limit");
        $page = $request->query->get("page");

        $repository = $this->get('doctrine')->getRepository(User::REPOSITORY);

        if ($page && $limit) {
            $users = $repository->findSubset($page, $limit);
        } else {
            $users = $repository->findAll();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserListResource($users), 200);
    }

    /**
     * @Route("/users/{id}", name="api_get_user", methods="GET")
     */
    public function getUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        $this->assertContentTypeIsValid($request);

        $user = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserResource($user), 200);
    }

    /**
     * @Route("/users", name="api_post_user", methods="POST")
     */
    public function postUserAction(Request $request)
    {
        $this->assertContentTypeIsValid($request);

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_USERS_POST);

        $this->assertSocialCredentialsIsValid($data);

        $user = $this->populateEntity(new User(), $data, ["region" => Region::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();

        $duplicateUser = $em->getRepository(User::REPOSITORY)
            ->findOneBy(["email" => $user->getEmail()]);

        if ($duplicateUser) {
            return $this->getErrorResponse(sprintf("User with email '%s' already exists", $user->getEmail()), 409);
        }

        $em->persist($user);
        $em->flush();

        // authenticate user
        $t = new UsernamePasswordToken($user, null, "main", $user->getRoles());
        $this->container->get("security.context")->setToken($t);

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserResource($user), 201);
    }

    /**
     * @Route("/users/{id}", name="api_put_user", methods="PUT")
     */
    public function putUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        $this->assertContentTypeIsValid($request);

        $user = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $data = $this->getData($request);
        $this->assertDataMatchesSchema($data, self::URI_USERS_PUT);

        $oldEmail = $user->getEmail();
        $user = $this->populateEntity($user, $data, ["region" => Region::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();

        if ($oldEmail !== $user->getEmail()) {
            $duplicateUser = $em->getRepository(User::REPOSITORY)
                ->findOneBy(["email" => $user->getEmail()]);

            if ($duplicateUser) {
                return $this->getErrorResponse(sprintf("User with email '%s' already exists", $user->getEmail()), 409);
            }
        }

        $em->persist($user);
        $em->flush();

        $jsonApiResponse = $this->get('engage360d_takeda.json_api_response');

        return new JsonResponse($jsonApiResponse->getUserResource($user), 200);
    }

    /**
     * @Route("/users/{id}", name="api_delete_user", methods="DELETE")
     */
    public function deleteUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        $this->assertContentTypeIsValid($request);

        $em = $this->get('doctrine')->getManager();

        $user = $em->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(["data" => (object) []], 200);
    }

    /**
     * @Route("/users/{id}/reset-password", name="api_post_user_reset_password", methods="POST")
     */
    function postUserResetPasswordAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        $this->assertContentTypeIsValid($request);

        $user = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $event = new UserEvent($user);
        $this->get('event_dispatcher')
            ->dispatch(Engage360dSecurityEvents::RESETTING_USER_PASSWORD, $event);

        return new JsonResponse(["data" => (object) []], 200);
    }
}
