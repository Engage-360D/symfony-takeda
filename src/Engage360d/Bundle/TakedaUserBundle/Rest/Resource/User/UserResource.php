<?php

namespace Engage360d\Bundle\TakedaUserBundle\Rest\Resource\User;

use GoIntegro\Hateoas\JsonApi\EntityResource;

class UserResource extends EntityResource
{
    public static $fieldWhitelist = [
        'id',
        'email',
        'firstname',
        'lastname',
        'birthday',
        'vkontakteId',
        'facebookId',
        'specializationExperienceYears',
        'specializationGraduationDate',
        'specializationInstitutionAddress',
        'specializationInstitutionName',
        'specializationInstitutionPhone',
        'specializationName',
        'roles',
    ];

    public function injectIsEnabled()
    {
        return $this->entity->getEnabled();
    }
}
