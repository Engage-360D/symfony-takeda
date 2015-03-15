<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TakedaJsonApiController extends JsonApiController
{
    public function assertSocialCredentialsIsValid($data)
    {
        if (isset($data->data->facebookId)) {
            $accessToken = isset($data->data->facebookToken) ? $data->data->facebookToken : "";

            if ($data->data->facebookId !== $this->getFacebookId($accessToken)) {
                throw new HttpException(400, "Provided facebookToken is not valid");
            }
        }

        if (isset($data->data->vkontakteId)) {
            $accessToken = isset($data->data->vkontakteToken) ? $data->data->vkontakteToken : "";

            if (!$this->isVkontakteCredentialsValid($data->data->vkontakteId, $accessToken)) {
                throw new HttpException(400, "Provided vkontakteId (or vkontakteToken) is not valid");
            }
        }

        if (isset($data->data->odnoklassnikiId)) {
            $accessToken = isset($data->data->odnoklassnikiToken) ? $data->data->odnoklassnikiToken : "";

            if (!$this->isOdnoklassnikiCredentialsValid($data->data->odnoklassnikiId, $accessToken)) {
                throw new HttpException(400, "Provided odnoklassnikiId (or odnoklassnikiToken) is not valid");
            }
        }

        if (isset($data->data->googleId)) {
            $accessToken = isset($data->data->googleToken) ? $data->data->googleToken : "";

            if (!$this->isGoogleCredentialsValid($accessToken)) {
                throw new HttpException(400, "Provided googleToken is not valid");
            }
        }
    }

    protected function getFacebookId($accessToken)
    {
        $buzz = $this->container->get('buzz');
        $response = $buzz->get('https://graph.facebook.com/v2.2/me?access_token=' . urlencode($accessToken));

        if (!$response->isSuccessful()) {
            return null;
        }

        $body = $response->getContent();
        $data = json_decode($body);

        return isset($data->id) ? $data->id : null;
    }

    protected function isVkontakteCredentialsValid($vkontakteId, $accessToken)
    {
        $buzz = $this->container->get('buzz');
        $response = $buzz->get(sprintf(
            'https://api.vk.com/method/users.isAppUser?v=5.27&user_id=%s&access_token=%s',
            $vkontakteId,
            urlencode($accessToken)
        ));

        if (!$response->isSuccessful()) {
            return false;
        }

        $body = $response->getContent();
        $data = json_decode($body);

        if (!isset($data->response) || $data->response !== 1) {
            return false;
        }

        return true;
    }

    public function isOdnoklassnikiCredentialsValid($odnoklassnikiId, $accessToken)
    {
        $buzz = $this->container->get('buzz');

        $response = $buzz->get(sprintf(
            'http://api.odnoklassniki.ru/fb.do?application_key=%s&method=users.getCurrentUser&access_token=%s&format=JSON&sig=%s',
            $this->container->getParameter('odnoklassniki_application_key'),
            $accessToken,
            md5(sprintf(
                'application_key=%sformat=JSONmethod=users.getCurrentUser%s',
                $this->container->getParameter('odnoklassniki_application_key'),
                md5($accessToken . $this->container->getParameter('odnoklassniki_client_secret'))
            ))
        ));

        if (!$response->isSuccessful()) {
            return false;
        }

        $body = $response->getContent();
        $data = json_decode($body);

        if (isset($data->error_code)) {
            return false;
        }

        return true;
    }

    public function isGoogleCredentialsValid($accessToken)
    {
        $buzz = $this->container->get('buzz');

        $response = $buzz->get(sprintf(
            'https://www.googleapis.com/plus/v1/people/me?access_token=%s',
            $accessToken
        ));

        if (!$response->isSuccessful()) {
            return false;
        }

        $body = $response->getContent();
        $data = json_decode($body);

        if (isset($data->error)) {
            return false;
        }

        return true;
    }
}
