<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;
use JsonSchema\Validator;
use JsonSchema\Uri\UriRetriever;

class TakedaJsonApiController extends JsonApiController
{
    // TODO remove
    protected function getSchemaValidatior($schemaFile, \stdClass $data)
    {
        $retriever = new UriRetriever();
        $schema = $retriever->retrieve(
            'file://' . $this->get('kernel')->getRootDir() . '/../web' .
            $schemaFile
        );

        $validator = new Validator();
        $validator->check($data, $schema);

        return $validator;
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

    protected function getUserArray(User $user)
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
            "specializationGraduationDate" => $user->getSpecializationGraduationDate() ? $user->getSpecializationGraduationDate()->format(\DateTime::ISO8601) : null,
            "specializationInstitutionAddress" => $user->getSpecializationInstitutionAddress(),
            "specializationInstitutionName" => $user->getSpecializationInstitutionName(),
            "specializationInstitutionPhone" => $user->getSpecializationInstitutionPhone(),
            "specializationName" => $user->getSpecializationName(),
            "roles" => $user->getRoles(),
            "isEnabled" => $user->getEnabled(),
            "links" => [
                "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null
            ],
        ];
    }

    protected function getUsersRegionLink()
    {
        return [
            "users.region" => [
                "href" => $this->getBaseUrl() . "/regions/{users.region}",
                "type" => "regions"
            ],
        ];
    }

    protected function getTestResultArray($testResult)
    {
        $recommendations = $testResult->getRecommendations();
        unset($recommendations['pages']);

        return [
            "id" => (String) $testResult->getId(),
            "sex" => $testResult->getSex(),
            "birthday" => $testResult->getBirthday()->format(\DateTime::ISO8601),
            "growth" => $testResult->getGrowth(),
            "weight" => $testResult->getWeight(),
            "isSmoker" => $testResult->getIsSmoker(),
            "cholesterolLevel" => $testResult->getCholesterolLevel(),
            "isCholesterolDrugsConsumer" => $testResult->getIsCholesterolDrugsConsumer(),
            "hasDiabetes" => $testResult->getHasDiabetes(),
            "hadSugarProblems" => $testResult->getHadSugarProblems(),
            "isSugarDrugsConsumer" => $testResult->getIsSugarDrugsConsumer(),
            "arterialPressure" => $testResult->getArterialPressure(),
            "isArterialPressureDrugsConsumer" => $testResult->getIsArterialPressureDrugsConsumer(),
            "physicalActivityMinutes"  => $testResult->getPhysicalActivityMinutes(),
            "hadHeartAttackOrStroke" => $testResult->getHadHeartAttackOrStroke(),
            "hadBypassSurgery" => $testResult->getHadBypassSurgery(),
            "isAddingExtraSalt" => $testResult->getIsAddingExtraSalt(),
            "isAcetylsalicylicDrugsConsumer" => $testResult->getIsAcetylsalicylicDrugsConsumer(),
            "bmi" => $testResult->getBmi(),
            "score" => $testResult->getScore(),
            "recommendations" => $recommendations,
            "createdAt" => $testResult->getCreatedAt()->format(\DateTime::ISO8601),
        ];
    }

    protected function getPageRecommendationArray($pageRecommendation)
    {
        return [
            "state" => $pageRecommendation['state'],
            "title" => $pageRecommendation['title'],
            "subtitle" => $pageRecommendation['subtitle'],
            "text" => $pageRecommendation['text'],
        ];
    }

    protected function getNewsLink()
    {
        return [
            "news.category" => [
                "href" => $this->getBaseUrl() . "/records/{news.category}",
                "type" => "records"
            ],
        ];
    }

    protected function getNewsArray($article) {
        return [
            "id" => (string) $article->getId(),
            "title" => $article->getTitle(),
            "content" => $article->getContent(),
            "isActive" => $article->getIsActive(),
            "createdAt" => $article->getCreatedAt()->format(\DateTime::ISO8601),
            "links" => [
                "category" => (string) $article->getCategory()->getId()
            ],
        ];
    }

    protected function getRecordArray($record)
    {
        return [
            "id" => (string) $record->getId(),
            "data" => $record->getData(),
            "keyword" => $record->getKeyword(),
        ];
    }

    protected function getOpinionLink()
    {
        return [
            "opinions.expert" => [
                "href" => $this->getBaseUrl() . "/experts/{opinions.expert}",
                "type" => "experts"
            ],
        ];
    }

    protected function getOpinionArray($opinion)
    {
        return [
            "id" => (string) $opinion->getId(),
            "title" => $opinion->getTitle(),
            "content" => $opinion->getContent(),
            "isActive" => $opinion->getIsActive(),
            "viewsCount" => $opinion->getViewsCount(),
            "createdAt" => $opinion->getCreatedAt()->format(\DateTime::ISO8601),
            "links" => [
                "expert" => (string) $opinion->getExpert()->getId()
            ],
        ];
    }

    protected function getExpertArray($expert)
    {
        return [
            "id" => (string) $expert->getId(),
            "photoUri" => $expert->getPhotoUri(),
            "name" => $expert->getName(),
            "description" => $expert->getDescription(),
        ];
    }

    protected function getPillLink()
    {
        return [
            "pills.user" => [
                "href" => $this->getBaseUrl() . "/users/{pills.user}",
                "type" => "users"
            ]
        ];
    }

    protected function getPillArray($pill)
    {
        return [
            "id" => (string) $pill->getId(),
            "name" => $pill->getName(),
            "quantity" => $pill->getQuantity(),
            "repeat" => $pill->getRepeat(),
            "time" => $pill->getTime()->format('H:i:s'),
            "sinceDate" => $pill->getSinceDate()->format(\DateTime::ISO8601),
            "tillDate" => $pill->getTillDate()->format(\DateTime::ISO8601),
            "links" => [
                "user" => (string) $pill->getUser()->getId()
            ]
        ];
    }

    protected function getPageBlockArray($pageBlock)
    {
        return [
            "id" => (string) $pageBlock->getId(),
            "type" => $pageBlock->getType(),
            "keyword" => $pageBlock->getKeyword(),
            "json" => $pageBlock->getJson(),
        ];
    }

    protected function getPageBlockId($pageBlock)
    {
        return (string) $pageBlock->getId();
    }

    protected function getPageLink()
    {
        return [
            "pages.pageBlocks" => [
                "href" => $this->getBaseUrl() . "/page-blocks/{pages.pageBlocks}",
                "type" => "pageBlocks"
            ]
        ];
    }

    protected function getPageArray($page)
    {
        return [
            "id" => (string) $page->getId(),
            "url" => $page->getUrl(),
            "title" => $page->getTitle(),
            "description" => $page->getDescription(),
            "keywords" => $page->getKeywords(),
            "isActive" => $page->getIsActive(),
            "isEditable" => $page->getIsEditable(),
            "links" => [
                "pageBlocks" => array_map([$this, 'getPageBlockId'], $page->getPageBlocks()->toArray())
            ]
        ];
    }
}