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
     * @ORM\Column(name="doctor", type="boolean", nullable=true)
     */
    private $doctor;

    /**
     * @var date $birthday
     *
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var string $region
     *
     * @ORM\Column(name="region", type="string", nullable=true)
     */
    private $region;

    /**
     * @var date $registration
     *
     * @ORM\Column(name="registration", type="date", nullable=false)
     */
    private $registration;

    /**
     * @var string $specialization
     *
     * @ORM\Column(name="specialization", type="string", nullable=true)
     */
    private $specialization;

    /**
     * @var integer $experience
     *
     * @ORM\Column(name="experience", type="integer", nullable=true)
     */
    private $experience;

    /**
     * @var string $address
     *
     * @ORM\Column(name="address", type="string", nullable=true)
     */
    private $address;

    /**
     * @var string $phone
     *
     * @ORM\Column(name="phone", type="string", nullable=true)
     */
    private $phone;

    /**
     * @var string $institution
     *
     * @ORM\Column(name="institution", type="string", nullable=true)
     */
    private $institution;

    /**
     * @var date $graduation
     *
     * @ORM\Column(name="graduation", type="date", nullable=true)
     */
    private $graduation;
    
    /**
     * @ORM\OneToMany(targetEntity="Engage360d\Bundle\TakedaTestBundle\Entity\TestResult", mappedBy="user")
     */
    protected $testResults;

    /**
     * @var boolean $confirmInformation
     *
     * @ORM\Column(name="confirm_information", type="boolean", nullable=true)
     */
    private $confirmInformation;

    /**
     * @var boolean $confirmPersonalization
     *
     * @ORM\Column(name="confirm_personalization", type="boolean", nullable=true)
     */
    private $confirmPersonalization;

    /**
     * @var date $confirmSubscription
     *
     * @ORM\Column(name="confirm_subscription", type="boolean", nullable=true)
     */
    private $confirmSubscription;

    /**
     * @var string $vkontakteId
     *
     * @ORM\Column(name="vkontakte_id", type="string", nullable=true)
     */
    protected $vkontakteId;

    /**
     * @var string $vkontakteAccessToken
     *
     * @ORM\Column(name="vkontakte_access_token", type="string", nullable=true)
     */
    protected $vkontakteAccessToken;

    public function __construct()
    {
        parent::__construct();
        $this->testResults = new ArrayCollection();
        $this->registration = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        if (!$this->username) {
            $this->username = $email;
        }

        return $this;
    }

    public function isDoctor()
    {
        return $this->doctor;
    }

    public function setDoctor($doctor)
    {
        $this->doctor = $doctor;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
  
    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getRegion()
    {
        return $this->region;
    }

    public function setRegion($region)
    {
        $this->region = $region;
    }

    public function getRegistration()
    {
        return $this->registration;
    }

    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;
    }

    public function getSpecialization()
    {
        $this->specialization;
    }

    public function setExperience($experience)
    {
        $this->experience = $experience;
    }

    public function getExperience()
    {
        return $this->experience;
    }

    public function setAddress($address)
    {
        $this->address = $address;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setInstitution($institution)
    {
        $this->institution = $institution;
    }

    public function getInstitution()
    {
        return $this->institution;
    }

    public function setGraduation($graduation)
    {
        $this->graduation = $graduation;
    }

    public function getGraduation()
    {
        return $this->graduation;
    }

    public function isConfirmInformation()
    {
        return $this->confirmInformation;
    }

    public function setConfirmInformation($confirmInformation)
    {
        $this->confirmInformation = $confirmInformation;
    }

    public function isConfirmPersonalization()
    {
        return $this->confirmPersonalization;
    }

    public function setConfirmPersonalization($confirmPersonalization)
    {
        $this->confirmPersonalization = $confirmPersonalization;
    }

    public function isConfirmSubscription()
    {
        return $this->confirmSubscription;
    }

    public function setConfirmSubscription($confirmSubscription)
    {
        $this->confirmSubscription = $confirmSubscription;
    }

    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;
    }

    public function getVkontaktekId()
    {
        return $this->vkontakteId;
    }

    public function setVkontakteData($bdata)
    {
    }

    public function setVkontakteAccessToken($token)
    {
        $this->vkontakteAccessToken = $token;
    }

    public function getVkontakteAccessToken()
    {
        return $this->vkontakteAccessToken;
    }
    
    public function getTestResults()
    {
        return $this->testResults;
    }
    
    public function addTestResult($testResult)
    {
        $this->testResults[] = $testResult;
    }
}
