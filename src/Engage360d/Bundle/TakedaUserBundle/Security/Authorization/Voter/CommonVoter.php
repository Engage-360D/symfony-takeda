<?php

namespace Engage360d\Bundle\TakedaUserBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CommonVoter implements VoterInterface
{
    const VIEW = 'view',
        EDIT = 'edit',
        DELETE = 'delete';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE]);
    }

    public function supportsClass($class)
    {
        return true;
    }

    /**
     * @param TokenInterface $token
     * @param mixed $user
     * @param array $attributes
     * @return integer
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        return VoterInterface::ACCESS_GRANTED;
    }
}

