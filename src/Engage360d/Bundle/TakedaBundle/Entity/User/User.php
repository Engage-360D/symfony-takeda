<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaBundle\Entity\User;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;
use Engage360d\Bundle\SecurityBundle\Entity\User\User as BaseUser;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class User extends BaseUser
{
    const REPOSITORY = 'Engage360dTakedaBundle:User\User';
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    private $isDoctor;

    /**
     * @var date $birthday
     *
     * @ORM\Column(name="birthday", type="date")
     */
    private $birthday;

    /**
     * @var $region
     *
     * @ORM\ManyToOne(targetEntity="Engage360d\Bundle\TakedaBundle\Entity\Region\Region")
     * @ORM\JoinColumn(name="region_id", referencedColumnName="id")
     */
    private $region;

    /**
     * @var string $specializationName
     *
     * @ORM\Column(name="specialization_name", type="string", nullable=true)
     */
    private $specializationName;

    /**
     * @var integer $specializationExperienceYears
     *
     * @ORM\Column(name="specialization_experience_years", type="integer", nullable=true)
     */
    private $specializationExperienceYears;

    /**
     * @var date $specializationGraduationDate
     *
     * @ORM\Column(name="specialization_graduation_date", type="date", nullable=true)
     */
    private $specializationGraduationDate;

    /**
     * @var string $specializationInstitutionAddress
     *
     * @ORM\Column(name="specialization_institution_address", type="string", nullable=true)
     */
    private $specializationInstitutionAddress;

    /**
     * @var string $specializationInstitutionPhone
     *
     * @ORM\Column(name="specialization_institution_phone", type="string", nullable=true)
     */
    private $specializationInstitutionPhone;

    /**
     * @var string $specializationInstitutionName
     *
     * @ORM\Column(name="specialization_institution_name", type="string", nullable=true)
     */
    private $specializationInstitutionName;

    /**
     * @ORM\OneToMany(targetEntity="Engage360d\Bundle\TakedaBundle\Entity\TestResult", mappedBy="user")
     */
    protected $testResults;

    /**
     * @var date $confirmSubscription
     *
     * @ORM\Column(name="is_subscribed", type="boolean")
     */
    private $isSubscribed = true;

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

    /**
     * @var datetime $createdAt
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        parent::__construct();
        $this->testResults = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getIsEnabled()
    {
        return $this->enabled;
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
        return $this->isDoctor;
    }

    public function setIsDoctor($isDoctor)
    {
        if ($isDoctor) {
            $this->addRole('ROLE_DOCTOR');
        }

        return $this;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function setBirthday($birthday)
    {
        if (is_string($birthday)) {
            $birthday = new \DateTime($birthday);
        }

        $this->birthday = $birthday;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
        if (!$this->username) {
            $this->username = $facebookId;
        }
    }

    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;
        if (!$this->username) {
            $this->username = $vkontakteId;
        }
    }

    public function getVkontakteId()
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

    public function setOdnoklassnikiId($vkontakteId)
    {
    }

    public function getOdnoklassnikiId()
    {
    }

    public function setOdnoklassnikiData($bdata)
    {
    }

    public function setOdnoklassnikiAccessToken($token)
    {
    }

    public function getOdnoklassnikieAccessToken()
    {
    }

    public function getFullName()
    {
        return implode(' ', array(
            $this->getFirstname(),
            $this->getLastname(),
        ));
    }

    public function getTestResults()
    {
        return $this->testResults;
    }

    public function addTestResult($testResult)
    {
        $this->testResults[] = $testResult;
    }

    /**
     * Set specializationName
     *
     * @param string $specializationName
     * @return User
     */
    public function setSpecializationName($specializationName)
    {
        $this->specializationName = $specializationName;

        return $this;
    }

    /**
     * Get specializationName
     *
     * @return string
     */
    public function getSpecializationName()
    {
        return $this->specializationName;
    }

    /**
     * Set specializationExperienceYears
     *
     * @param integer $specializationExperienceYears
     * @return User
     */
    public function setSpecializationExperienceYears($specializationExperienceYears)
    {
        $this->specializationExperienceYears = $specializationExperienceYears;

        return $this;
    }

    /**
     * Get specializationExperienceYears
     *
     * @return integer
     */
    public function getSpecializationExperienceYears()
    {
        return $this->specializationExperienceYears;
    }

    /**
     * Set specializationInstitutionAddress
     *
     * @param string $specializationInstitutionAddress
     * @return User
     */
    public function setSpecializationInstitutionAddress($specializationInstitutionAddress)
    {
        $this->specializationInstitutionAddress = $specializationInstitutionAddress;

        return $this;
    }

    /**
     * Get specializationInstitutionAddress
     *
     * @return string
     */
    public function getSpecializationInstitutionAddress()
    {
        return $this->specializationInstitutionAddress;
    }

    /**
     * Set specializationInstitutionPhone
     *
     * @param string $specializationInstitutionPhone
     * @return User
     */
    public function setSpecializationInstitutionPhone($specializationInstitutionPhone)
    {
        $this->specializationInstitutionPhone = $specializationInstitutionPhone;

        return $this;
    }

    /**
     * Get specializationInstitutionPhone
     *
     * @return string
     */
    public function getSpecializationInstitutionPhone()
    {
        return $this->specializationInstitutionPhone;
    }

    /**
     * Set specializationInstitutionName
     *
     * @param string $specializationInstitutionName
     * @return User
     */
    public function setSpecializationInstitutionName($specializationInstitutionName)
    {
        $this->specializationInstitutionName = $specializationInstitutionName;

        return $this;
    }

    /**
     * Get specializationInstitutionName
     *
     * @return string
     */
    public function getSpecializationInstitutionName()
    {
        return $this->specializationInstitutionName;
    }

    /**
     * Set isSubscribed
     *
     * @param boolean $isSubscribed
     * @return User
     */
    public function setIsSubscribed($isSubscribed)
    {
        $this->isSubscribed = $isSubscribed;

        return $this;
    }

    /**
     * Get isSubscribed
     *
     * @return boolean
     */
    public function getIsSubscribed()
    {
        return $this->isSubscribed;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist
     *
     * @param \DateTime $createdAt
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set region
     *
     * @param \Engage360d\Bundle\TakedaBundle\Entity\Region\Region $region
     * @return User
     */
    public function setRegion(\Engage360d\Bundle\TakedaBundle\Entity\Region\Region $region = null)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return \Engage360d\Bundle\TakedaBundle\Entity\Region\Region
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Remove testResults
     *
     * @param \Engage360d\Bundle\TakedaBundle\Entity\TestResult $testResults
     */
    public function removeTestResult(\Engage360d\Bundle\TakedaBundle\Entity\TestResult $testResults)
    {
        $this->testResults->removeElement($testResults);
    }

    /**
     * Set specializationGraduationDate
     *
     * @param \DateTime $specializationGraduationDate
     * @return User
     */
    public function setSpecializationGraduationDate($specializationGraduationDate)
    {
        if (is_string($specializationGraduationDate)) {
            $specializationGraduationDate = new \DateTime($specializationGraduationDate);
        }

        $this->specializationGraduationDate = $specializationGraduationDate;

        return $this;
    }

    /**
     * Get specializationGraduationDate
     *
     * @return \DateTime
     */
    public function getSpecializationGraduationDate()
    {
        return $this->specializationGraduationDate;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return User
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }
}
