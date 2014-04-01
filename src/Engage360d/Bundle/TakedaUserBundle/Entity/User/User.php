<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\Entity\User;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Engage360d\Bundle\SecurityBundle\Entity\User\User as BaseUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var date $birthday
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var date $registration
     *
     * @ORM\Column(name="registration", type="date", nullable=false)
     */
    private $registration;

    public function __construct()
    {
        parent::__construct();
        $this->registration = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
        if (!$this->username) {
            $this->username = $firstname;
        }
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
  
    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getRegistration()
    {
        return $this->registration;
    }
}
