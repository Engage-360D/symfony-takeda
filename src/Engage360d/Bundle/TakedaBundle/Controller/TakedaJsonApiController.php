<?php

namespace Engage360d\Bundle\TakedaBundle\Controller;

use Engage360d\Bundle\JsonApiBundle\Controller\JsonApiController;
use Engage360d\Bundle\TakedaBundle\Entity\User\User;

class TakedaJsonApiController extends JsonApiController
{
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

    protected function getUsersRegionLink()
    {
        return [
            "users.region" => [
                "href" => $this->getBaseUrl() . "/regions/{users.region}",
                "type" => "regions"
            ]
        ];
    }

    protected function getTestResultArray($testResult)
    {
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
            "isAddingExtraSalt" => $testResult->getIsAddingExtraSalt(),
            "isAcetylsalicylicDrugsConsumer" => $testResult->getIsAcetylsalicylicDrugsConsumer(),
            "bmi" => $testResult->getBmi(),
            "score" => $testResult->getScore(),
            "recommendations" => $testResult->getRecommendations()
        ];
    }
}