<?php

namespace Engage360d\Bundle\TakedaUserBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;
use Engage360d\Bundle\TakedaUserBundle\Entity\User\User;
use Engage360d\Bundle\TakedaUserBundle\Entity\Region\Region;

class UserController extends JsonApiController
{
    const URI_USERS_LIST = '/api/v1/schemas/users/list.json';
    const URI_USERS_ONE = '/api/v1/schemas/users/one.json';
    const URI_USERS_POST = '/api/v1/schemas/users/post.json';
    const URI_USERS_PUT = '/api/v1/schemas/users/put.json';

    /**
     * @Route("/users", name="api_get_users", methods="GET")
     */
    public function getUsersAction(Request $request)
    {
        // access control lets here only authorized users
        $user = $this->get('security.context')->getToken()->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $limit = $request->query->get("limit");
        $page = $request->query->get("page");

        $repository = $this->get('doctrine')->getRepository(User::REPOSITORY);

        if ($page && $limit) {
            $users = $repository->findSubset($page, $limit);
        } else {
            $users = $repository->findAll();
        }

        $response = [
            "links" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getRegionLink']),
            "data" => array_map(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getUserJson'], $users)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/users/{id}", name="api_get_user", methods="GET")
     */
    public function getUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->get('security.context')->getToken()->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $user = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            return $this->getErrorResponse(sprintf("User with id = %s not found", $id), 404);
        }

        $response = [
            "links" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getRegionLink']),
            "data" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getUserJson'], $user)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/users", name="api_post_user", methods="POST")
     */
    public function postUserAction(Request $request)
    {
        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_USERS_POST
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $user = $this->populateEntity(new User(), $data, ["region" => Region::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();

        $duplicateUser = $em->getRepository(User::REPOSITORY)
            ->findOneBy(["email" => $user->getEmail()]);

        if ($duplicateUser) {
            return $this->getErrorResponse(sprintf("User with email '%s' already exists", $user->getEmail()), 400);
        }

        $em->persist($user);
        $em->flush();

        $response = [
            "links" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getRegionLink']),
            "data" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getUserJson'], $user)
        ];

        return new JsonResponse($response, 201);
    }

    /**
     * @Route("/users/{id}", name="api_put_user", methods="PUT")
     */
    public function putUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->get('security.context')->getToken()->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $user = $this->get('doctrine')->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            return $this->getErrorResponse(sprintf("User with id = %s not found", $id), 404);
        }

        $json = $request->getContent();
        $data = json_decode($json);

        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            self::URI_USERS_PUT
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        if (!$validator->isValid()) {
            return $this->getErrorResponse($validator->getErrors(), 400);
        }

        $user = $this->populateEntity($user, $data, ["region" => Region::REPOSITORY]);

        $em = $this->get('doctrine')->getManager();

        $em->persist($user);
        $em->flush();

        $response = [
            "links" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getRegionLink']),
            "data" => call_user_func(['Engage360d\Bundle\TakedaUserBundle\Controller\Api\UserController', 'getUserJson'], $user)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/users", name="api_delete_user", methods="DELETE")
     */
    public function deleteUserAction(Request $request, $id)
    {
        // access control lets here only authorized users
        $user = $this->get('security.context')->getToken()->getUser();

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->getErrorResponse("Forbidden", 403);
        }

        if (!$this->isContentTypeValid($request)) {
            return $this->getInvalidContentTypeResponse();
        }

        $em = $this->get('doctrine')->getManager();

        $user = $em->getRepository(User::REPOSITORY)
            ->findOneById($id);

        if (!$user) {
            return $this->getErrorResponse(sprintf("User with id = %s not found", $id), 404);
        }

        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, 200);
    }

    public static function getRegionLink()
    {
        return [
            "users.region" => [
                "href" => "https://cardiomagnyl.ru/api/v1/regions/{users.region}",
                "type" => "regions"
            ]
        ];
    }

    public static function getUserJson(User $user)
    {
        return [
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
                "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null
            ]
        ];
    }
}