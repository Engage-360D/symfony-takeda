<?php

namespace Engage360d\Bundle\TakedaBundle\Services;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;

/**
 * Class JsonApiResponse
 * @package Engage360d\Bundle\TakedaBundle\Services
 */
class JsonApiResponse extends ContainerAware
{
    protected function getBaseUrl()
    {
        return $this->container->getParameter('api.base_url');
    }

    public function getTokenResource($token, $user)
    {
        return [
            "links" => [
                "tokens.user" => [
                    "href" => $this->getBaseUrl() . "/users/{tokens.user}",
                    "type" => "users"
                ]
            ],
            "data" => [
                "id" => $token,
                "links" => [
                    "user" => (String) $user->getId()
                ]
            ],
            "linked" => [
                "users" => [$this->getUserArray($user)]
            ]
        ];
    }

    public function getUserArray(User $user)
    {
        return [
            "id" => (String) $user->getId(),
            "email" => $user->getEmail(),
            "firstname" => $user->getFirstname(),
            "lastname" => $user->getLastname(),
            "birthday" => $user->getBirthday()->format(\DateTime::ISO8601),
            "vkontakteId" => $user->getVkontakteId(),
            "facebookId" => $user->getFacebookId(),
            "odnoklassnikiId" => $user->getOdnoklassnikiId(),
            "googleId" => $user->getGoogleId(),
            "specializationExperienceYears" => $user->getSpecializationExperienceYears(),
            "specializationGraduationDate" => $user->getSpecializationGraduationDate() ? $user->getSpecializationGraduationDate()->format(\DateTime::ISO8601) : null,
            "specializationInstitutionAddress" => $user->getSpecializationInstitutionAddress(),
            "specializationInstitutionName" => $user->getSpecializationInstitutionName(),
            "specializationInstitutionPhone" => $user->getSpecializationInstitutionPhone(),
            "specializationName" => $user->getSpecializationName(),
            "roles" => $user->getRoles(),
            "isEnabled" => $user->getEnabled(),
            "resetAt" => $user->getResetAt() ? $user->getResetAt()->format(\DateTime::ISO8601) : null,
            "links" => [
                "region" => $user->getRegion() ? (String) $user->getRegion()->getId() : null
            ],
        ];
    }

    public function getUsersRegionLink()
    {
        return [
            "users.region" => [
                "href" => $this->getBaseUrl() . "/regions/{users.region}",
                "type" => "regions"
            ],
        ];
    }

    public function getUserResource($user)
    {
        return [
            "links" => $this->getUsersRegionLink(),
            "data" => $this->getUserArray($user)
        ];
    }

    public function getUserListResource($users)
    {
        return [
            "links" => $this->getUsersRegionLink(),
            "data" => array_map([$this, 'getUserArray'], $users)
        ];
    }

    public function getTestResultArray($testResult)
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

    public function getTestResultResource($testResult)
    {
        return [
            "data" => $this->getTestResultArray($testResult)
        ];
    }

    public function getTestResultListResource($testResults)
    {
        return [
            "data" => array_map([$this, 'getTestResultArray'], $testResults)
        ];
    }

    public function getPageRecommendationArray($pageRecommendation)
    {
        return [
            "state" => $pageRecommendation['state'],
            "title" => $pageRecommendation['title'],
            "subtitle" => $pageRecommendation['subtitle'],
            "text" => $pageRecommendation['text'],
        ];
    }

    public function getNewsLink()
    {
        return [
            "news.category" => [
                "href" => $this->getBaseUrl() . "/records/{news.category}",
                "type" => "records"
            ],
        ];
    }

    public function getNewsArray($article) {
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

    public function getNewsResource($newsArticle)
    {
        return [
            "links" => $this->getNewsLink(),
            "data" => $this->getNewsArray($newsArticle),
            "linked" => [
                "records" => [
                    $this->getRecordArray($newsArticle->getCategory())
                ]
            ]
        ];
    }

    public function getNewsListResource($news)
    {
        return [
            "links" => $this->getNewsLink(),
            "data" => array_map([$this, 'getNewsArray'], $news)
        ];
    }

    public function getRecordArray($record)
    {
        return [
            "id" => (string) $record->getId(),
            "data" => $record->getData(),
            "keyword" => $record->getKeyword(),
            "order" => $record->getOrder(),
        ];
    }

    public function getRecordResource($record)
    {
        return [
            "data" => $this->getRecordArray($record),
        ];
    }

    public function getRecordListResource($records)
    {
        return [
            "data" => array_map([$this, 'getRecordArray'], $records),
        ];
    }

    public function getOpinionLink()
    {
        return [
            "opinions.expert" => [
                "href" => $this->getBaseUrl() . "/experts/{opinions.expert}",
                "type" => "experts"
            ],
        ];
    }

    public function getOpinionArray($opinion)
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

    public function getOpinionResource($opinion)
    {
        return [
            "links" => $this->getOpinionLink(),
            "data" => $this->getOpinionArray($opinion),
            "linked" => [
                "experts" => [
                    $this->getExpertArray($opinion->getExpert())
                ]
            ]
        ];
    }

    public function getOpinionListResource($opinions)
    {
        return [
            "links" => $this->getOpinionLink(),
            "data" => array_map([$this, 'getOpinionArray'], $opinions)
        ];
    }

    public function getExpertArray($expert)
    {
        return [
            "id" => (string) $expert->getId(),
            "photoUri" => $expert->getPhotoUri(),
            "name" => $expert->getName(),
            "description" => $expert->getDescription(),
        ];
    }

    public function getExpertResource($expert)
    {
        return [
            "data" => $this->getExpertArray($expert),
        ];
    }

    public function getExpertListResource($experts)
    {
        return [
            "data" => array_map([$this, 'getExpertArray'], $experts)
        ];
    }

    public function getPillLink()
    {
        return [
            "pills.user" => [
                "href" => $this->getBaseUrl() . "/users/{pills.user}",
                "type" => "users"
            ]
        ];
    }

    public function getPillArray($pill)
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

    public function getPillResource($pill)
    {
        return [
            "links" => $this->getPillLink(),
            "data" => $this->getPillArray($pill),
        ];
    }

    public function getPillListResource($pills)
    {
        return [
            "links" => $this->getPillLink(),
            "data" => array_map([$this, 'getPillArray'], $pills),
        ];
    }

    public function getPageBlockArray($pageBlock)
    {
        return [
            "id" => (string) $pageBlock->getId(),
            "type" => $pageBlock->getType(),
            "keyword" => $pageBlock->getKeyword(),
            "json" => $pageBlock->getJson(),
        ];
    }

    public function getPageBlockId($pageBlock)
    {
        return (string) $pageBlock->getId();
    }

    public function getPageLink()
    {
        return [
            "pages.pageBlocks" => [
                "href" => $this->getBaseUrl() . "/page-blocks/{pages.pageBlocks}",
                "type" => "pageBlocks"
            ]
        ];
    }

    // this violates JSON API spec
    public function getPageArray($page)
    {
        return [
            "id" => (string) $page->getId(),
            "url" => $page->getUrl(),
            "title" => $page->getTitle(),
            "description" => $page->getDescription(),
            "keywords" => $page->getKeywords(),
            "isActive" => $page->getIsActive(),
            "isEditable" => $page->getIsEditable(),
            "pageBlocks" => array_map([$this, 'getPageBlockArray'], $page->getPageBlocks()->toArray())
        ];
    }

    public function getPageResource($page)
    {
        return [
            "links" => $this->getPageLink(),
            "data"  => $this->getPageArray($page),
            "linked" => [
                "pageBlocks" => array_map([$this, 'getPageBlockArray'], $page->getPageBlocks()->toArray())
            ]
        ];
    }

    public function getPageListResource($pages)
    {
        return [
            "links" => $this->getPageLink(),
            "data"  => array_map([$this, 'getPageArray'], $pages),
        ];
    }

    public function getRegionArray($region)
    {
        return [
            "id" => (String) $region->getId(),
            "name" => $region->getName(),
        ];
    }

    public function getRegionResource($region)
    {
        return [
            "data" => $this->getRegionArray($region)
        ];
    }

    public function getRegionListResource($regions)
    {
        return [
            "data" => array_map([$this, 'getRegionArray'], $regions)
        ];
    }

    public function getIncidentsResource($testResult)
    {
        return [
            "data" => [
                "hadBypassSurgery" => $testResult->getHadBypassSurgery(),
                "hadHeartAttackOrStroke" => $testResult->getHadHeartAttackOrStroke(),
                "hasDiabetes" => $testResult->getHasDiabetes(),
            ]
        ];
    }

    public function getFileArray($file)
    {
        return [
            "id" => (string) $file->getId(),
            "url" => $file->getFileUri(),
        ];
    }

    public function getFileResource($file)
    {
        return ["data" => $this->getFileArray($file)];
    }

    public function getFileListResource($files)
    {
        return [
            "data" => array_map([$this, 'getFileArray'], $files)
        ];
    }
}