<?php

namespace Engage360d\Bundle\TakedaTestBundle\Entity;

use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Engage360d\Bundle\TakedaTestBundle\Score\Calculator as ScoreCalculator;
use Engage360d\Bundle\TakedaTestBundle\Recommendations;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_results")
 */
class TestResult
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", name="sex", length=6)
     */
    protected $sex;
    
    /**
     * @ORM\Column(type="date")
     */
    protected $birthday;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $growth;
    
    /**
     * @ORM\Column(type="integer")
     */
    protected $weight;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $smoking;
    
    /**
     * @ORM\Column(type="integer", name="cholesterol_level")
     */
    protected $cholesterolLevel;
    
    /**
     * @ORM\Column(type="boolean", nullable=true, name="cholesterol_drugs")
     */
    protected $cholesterolDrugs;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $diabetes;
    
    /**
     * @ORM\Column(type="boolean", nullable=true, name="sugar_problems")
     */
    protected $sugarProblems;
    
    /**
     * @ORM\Column(type="boolean", nullable=true, name="sugar_drugs")
     */
    protected $sugarDrugs;
    
    /**
     * @ORM\Column(type="integer", name="arterial_pressure")
     */
    protected $arterialPressure;
    
    /**
     * @ORM\Column(type="boolean", nullable=true, name="arterial_pressure_drugs")
     */
    protected $arterialPressureDrugs;
    
    /**
     * @ORM\Column(type="integer", name="physical_activity")
     */
    protected $physicalActivity;
    
    /**
     * @ORM\Column(type="boolean", name="heart_attack_or_stroke")
     */
    protected $heartAttackOrStroke;
    
    /**
     * @ORM\Column(type="boolean", name="extra_salt")
     */
    protected $extraSalt;
    
    /**
     * @ORM\Column(type="boolean", name="acetylsalicylic_drugs")
     */
    protected $acetylsalicylicDrugs;

    /**
     * @ORM\ManyToOne(targetEntity="Engage360d\Bundle\TakedaUserBundle\Entity\User\User", inversedBy="testResults")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function setSex($sex)
    {
        $this->sex = $sex;
    }
    
    public function getSex()
    {
        return $this->sex;
    }
    
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;
    }
    
    public function getBirthday()
    {
        return $this->birthday;
    }
    
    public function setGrowth($growth)
    {
        $this->growth = $growth;
    }
    
    public function getGrowth()
    {
        return $this->growth;
    }
    
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
    
    public function getWeight()
    {
        return $this->weight;
    }
    
    public function setSmoking($smoking)
    {
        $this->smoking = $smoking;
    }
    
    public function isSmoking()
    {
        return $this->smoking;
    }
    
    public function setCholesterolLevel($cholesterolLevel)
    {
        $this->cholesterolLevel = $cholesterolLevel;
    }
    
    public function getCholesterolLevel()
    {
        return $this->cholesterolLevel;
    }
    
    public function setCholesterolDrugs($cholesterolDrugs)
    {
        $this->cholesterolDrugs = $cholesterolDrugs;
    }
    
    public function isCholesterolDrugs()
    {
        return $this->cholesterolDrugs;
    }
    
    public function setDiabetes($diabetes)
    {
        $this->diabetes = $diabetes;
    }
    
    public function isDiabetes()
    {
        return $this->diabetes;
    }
    
    public function setSugarProblems($sugarProblems)
    {
        $this->sugarProblems = $sugarProblems;
    }
    
    public function isSugarProblems()
    {
        return $this->sugarProblems;
    }
    
    public function setSugarDrugs($sugarDrugs)
    {
        $this->sugarDrugs = $sugarDrugs;
    }
    
    public function isSugarDrugs()
    {
        return $this->sugarDrugs;
    }
    
    public function setArterialPressure($arterialPressure)
    {
        $this->arterialPressure = $arterialPressure;
    }
    
    public function getArterialPressure()
    {
        return $this->arterialPressure;
    }
    
    public function setArterialPressureDrugs($arterialPressureDrugs)
    {
        $this->arterialPressureDrugs = $arterialPressureDrugs;
    }
    
    public function isArterialPressureDrugs()
    {
        return $this->arterialPressureDrugs;
    }
    
    public function setPhysicalActivity($physicalActivity)
    {
        $this->physicalActivity = $physicalActivity;
    }
    
    public function getPhysicalActivity()
    {
        return $this->physicalActivity;
    }
    
    public function setHeartAttackOrStroke($heartAttackOrStroke)
    {
        $this->heartAttackOrStroke = $heartAttackOrStroke;
    }
    
    public function isHeartAttackOrStroke()
    {
        return $this->heartAttackOrStroke;
    }
    
    public function setExtraSalt($extraSalt)
    {
        $this->extraSalt = $extraSalt;
    }
    
    public function isExtraSalt()
    {
        return $this->extraSalt;
    }
    
    public function setAcetylsalicylicDrugs($acetylsalicylicDrugs)
    {
        $this->acetylsalicylicDrugs = $acetylsalicylicDrugs;
    }
    
    public function isAcetylsalicylicDrugs()
    {
        return $this->acetylsalicylicDrugs;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getBmi()
    {
        return $this->weight / pow($this->growth / 100, 2);
    }
    
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    public function getScoreValue()
    {
        return ScoreCalculator::calculate(
            $this->getSex(),
            $this->getBirthday()->diff(new \DateTime())->y,
            $this->isSmoking(),
            $this->getArterialPressure(),
            $this->getCholesterolLevel()
        );
    }

    public function getRecommendations()
    {
        return Recommendations::match(
            $this->getId(),
            $this->isSmoking(),
            $this->getArterialPressure(),
            $this->isExtraSalt(),
            $this->getCholesterolLevel(),
            $this->getPhysicalActivity(),
            $this->getBmi()
        );
    }

    public function validateSugarProblemsOrDrugs(ExecutionContextInterface $context)
    {
        if ($this->diabetes) {
            if ($this->sugarProblems === null) {
                $context->addViolationAt(
                    'sugarProblems',
                    'This value should not be blank.',
                    array(),
                    null
                );
            }
        } else {
            if ($this->sugarDrugs === null) {
                $context->addViolationAt(
                    'sugarDrugs',
                    'This value should not be blank.',
                    array(),
                    null
                );
            }
        }
    }
}
