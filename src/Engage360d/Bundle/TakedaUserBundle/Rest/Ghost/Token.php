<?php

namespace Engage360d\Bundle\TakedaUserBundle\Rest\Ghost;

// Entidades.
use GoIntegro\Bundle\SomeBundle\Entity\Star;
// JSON-API.
use GoIntegro\Hateoas\JsonApi\GhostResourceEntity,
    GoIntegro\Hateoas\Metadata\Resource\ResourceRelationship,
    GoIntegro\Hateoas\Metadata\Resource\ResourceRelationships;
use Symfony\Component\Security\Core\User\UserInterface;
// Colecciones.
use Doctrine\Common\Collections\ArrayCollection;

class Token implements GhostResourceEntity
{
    private $id;

    private $user;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    public function getUser()
    {
        return $this->user;
    }

    public static function getRelationships()
    {
        $relationships = new ResourceRelationships;

        $relationships->toOne['user'] = new ResourceRelationship(
            'Engage360d\Bundle\TakedaUserBundle\Entity\User\User',
            'users',
            'users',
            'toOne',
            'users',
            'users'
        );

        return $relationships;
    }
}
