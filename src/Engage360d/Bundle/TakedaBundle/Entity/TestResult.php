<?php

namespace Engage360d\Bundle\TakedaBundle\Entity;

use Symfony\Component\Validator\ExecutionContextInterface;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Engage360d\Bundle\TakedaBundle\Score\Calculator as ScoreCalculator;
use Engage360d\Bundle\TakedaBundle\Recommendations;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_results")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
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
     * @ORM\Column(name="is_smoker", type="boolean")
     */
    protected $isSmoker;

    /**
     * @ORM\Column(type="integer", name="cholesterol_level", nullable=true)
     */
    protected $cholesterolLevel;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_cholesterol_drug_consumer")
     */
    protected $isCholesterolDrugsConsumer;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $hasDiabetes;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="had_sugar_problems")
     */
    protected $hadSugarProblems;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_sugar_drug_consumer")
     */
    protected $isSugarDrugsConsumer;

    /**
     * @ORM\Column(type="integer", name="arterial_pressure")
     */
    protected $arterialPressure;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="is_arterial_pressure_drugs_consumer")
     */
    protected $isArterialPressureDrugsConsumer;

    /**
     * @ORM\Column(type="integer", name="physical_activity_minutes")
     */
    protected $physicalActivityMinutes;

    /**
     * @ORM\Column(type="boolean", name="had_heart_attack_or_stroke")
     */
    protected $hadHeartAttackOrStroke;

    /**
     * @ORM\Column(type="boolean", name="is_adding_extrasalt")
     */
    protected $isAddingExtraSalt;

    /**
     * @ORM\Column(type="boolean", name="is_acetylsalicylic_gruds_consumer")
     */
    protected $isAcetylsalicylicDrugsConsumer;

    /**
     * @ORM\ManyToOne(targetEntity="Engage360d\Bundle\TakedaBundle\Entity\User\User", inversedBy="testResults", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    private $score;

    private $recommendations;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

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
        if (is_string($birthday)) {
            $birthday = new \DateTime($birthday);
        }

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

    public function setCholesterolLevel($cholesterolLevel)
    {
        $this->cholesterolLevel = $cholesterolLevel;
    }

    public function getCholesterolLevel()
    {
        return $this->cholesterolLevel;
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
        return round($this->weight / pow($this->growth / 100, 2), 1);
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setRecommendations($recommendations)
    {
        $this->recommendations = $recommendations;
    }

    public function getRecommendations()
    {
        return $this->recommendations;
    }

    public function validateSugarProblemsOrDrugs(ExecutionContextInterface $context)
    {
        if ($this->hasDiabetes) {
            if ($this->hadSugarProblems === null) {
                $context->addViolationAt(
                    'hadSugarProblems',
                    'This value should not be blank.',
                    array(),
                    null
                );
            }
        } else {
            if ($this->isSugarDrugsConsumer === null) {
                $context->addViolationAt(
                    'isSugarDrugsConsumer',
                    'This value should not be blank.',
                    array(),
                    null
                );
            }
        }
    }

    /**
     * Set isSmoker
     *
     * @param boolean $isSmoker
     * @return TestResult
     */
    public function setIsSmoker($isSmoker)
    {
        $this->isSmoker = $isSmoker;

        return $this;
    }

    /**
     * Get isSmoker
     *
     * @return boolean
     */
    public function getIsSmoker()
    {
        return $this->isSmoker;
    }

    /**
     * Set isCholesterolDrugsConsumer
     *
     * @param boolean $isCholesterolDrugsConsumer
     * @return TestResult
     */
    public function setIsCholesterolDrugsConsumer($isCholesterolDrugsConsumer)
    {
        $this->isCholesterolDrugsConsumer = $isCholesterolDrugsConsumer;

        return $this;
    }

    /**
     * Get isCholesterolDrugsConsumer
     *
     * @return boolean
     */
    public function getIsCholesterolDrugsConsumer()
    {
        return $this->isCholesterolDrugsConsumer;
    }

    /**
     * Set hasDiabetes
     *
     * @param boolean $hasDiabetes
     * @return TestResult
     */
    public function setHasDiabetes($hasDiabetes)
    {
        $this->hasDiabetes = $hasDiabetes;

        return $this;
    }

    /**
     * Get hasDiabetes
     *
     * @return boolean
     */
    public function getHasDiabetes()
    {
        return $this->hasDiabetes;
    }

    /**
     * Set hadSugarProblems
     *
     * @param boolean $hadSugarProblems
     * @return TestResult
     */
    public function setHadSugarProblems($hadSugarProblems)
    {
        $this->hadSugarProblems = $hadSugarProblems;

        return $this;
    }

    /**
     * Get hadSugarProblems
     *
     * @return boolean
     */
    public function getHadSugarProblems()
    {
        return $this->hadSugarProblems;
    }

    /**
     * Set isSugarDrugsConsumer
     *
     * @param boolean $isSugarDrugsConsumer
     * @return TestResult
     */
    public function setIsSugarDrugsConsumer($isSugarDrugsConsumer)
    {
        $this->isSugarDrugsConsumer = $isSugarDrugsConsumer;

        return $this;
    }

    /**
     * Get isSugarDrugsConsumer
     *
     * @return boolean
     */
    public function getIsSugarDrugsConsumer()
    {
        return $this->isSugarDrugsConsumer;
    }

    /**
     * Set arterialPressure
     *
     * @param integer $arterialPressure
     * @return TestResult
     */
    public function setArterialPressure($arterialPressure)
    {
        $this->arterialPressure = $arterialPressure;

        return $this;
    }

    /**
     * Get arterialPressure
     *
     * @return integer
     */
    public function getArterialPressure()
    {
        return $this->arterialPressure;
    }

    /**
     * Set isArterialPressureDrugsConsumer
     *
     * @param boolean $isArterialPressureDrugsConsumer
     * @return TestResult
     */
    public function setIsArterialPressureDrugsConsumer($isArterialPressureDrugsConsumer)
    {
        $this->isArterialPressureDrugsConsumer = $isArterialPressureDrugsConsumer;

        return $this;
    }

    /**
     * Get isArterialPressureDrugsConsumer
     *
     * @return boolean
     */
    public function getIsArterialPressureDrugsConsumer()
    {
        return $this->isArterialPressureDrugsConsumer;
    }

    /**
     * Set physicalActivityMinutes
     *
     * @param integer $physicalActivityMinutes
     * @return TestResult
     */
    public function setPhysicalActivityMinutes($physicalActivityMinutes)
    {
        $this->physicalActivityMinutes = $physicalActivityMinutes;

        return $this;
    }

    /**
     * Get physicalActivityMinutes
     *
     * @return integer
     */
    public function getPhysicalActivityMinutes()
    {
        return $this->physicalActivityMinutes;
    }

    /**
     * Set hadHeartAttackOrStroke
     *
     * @param boolean $hadHeartAttackOrStroke
     * @return TestResult
     */
    public function setHadHeartAttackOrStroke($hadHeartAttackOrStroke)
    {
        $this->hadHeartAttackOrStroke = $hadHeartAttackOrStroke;

        return $this;
    }

    /**
     * Get hadHeartAttackOrStroke
     *
     * @return boolean
     */
    public function getHadHeartAttackOrStroke()
    {
        return $this->hadHeartAttackOrStroke;
    }

    /**
     * Set isAddingExtraSalt
     *
     * @param boolean $isAddingExtraSalt
     * @return TestResult
     */
    public function setIsAddingExtraSalt($isAddingExtraSalt)
    {
        $this->isAddingExtraSalt = $isAddingExtraSalt;

        return $this;
    }

    /**
     * Get isAddingExtraSalt
     *
     * @return boolean
     */
    public function getIsAddingExtraSalt()
    {
        return $this->isAddingExtraSalt;
    }

    /**
     * Set isAcetylsalicylicDrugsConsumer
     *
     * @param boolean $isAcetylsalicylicDrugsConsumer
     * @return TestResult
     */
    public function setIsAcetylsalicylicDrugsConsumer($isAcetylsalicylicDrugsConsumer)
    {
        $this->isAcetylsalicylicDrugsConsumer = $isAcetylsalicylicDrugsConsumer;

        return $this;
    }

    /**
     * Get isAcetylsalicylicDrugsConsumer
     *
     * @return boolean
     */
    public function getIsAcetylsalicylicDrugsConsumer()
    {
        return $this->isAcetylsalicylicDrugsConsumer;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return TestResult
     */
    public function setCreatedAt($createdAt)
    {
        if (is_string($createdAt)) {
            $createdAt = new \DateTime($createdAt);
        }

        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Set score
     *
     * @param integer $score
     * @return TestResult
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Get score
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return TestResult
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
