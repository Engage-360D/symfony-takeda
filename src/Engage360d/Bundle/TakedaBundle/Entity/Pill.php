<?php

namespace Engage360d\Bundle\TakedaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="pills")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Pill
{
    const REPOSITORY = 'Engage360dTakedaBundle:Timeline\Pill';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Engage360d\Bundle\TakedaBundle\Entity\User\User", cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(name="quantity", type="integer")
     */
    protected $quantity;

    /**
     * @ORM\Column(name="`repeat`", type="string")
     */
    protected $repeat;

    /**
     * @ORM\Column(type="time")
     */
    protected $time;

    /**
     * @ORM\Column(name="since_date", type="datetime")
     */
    protected $sinceDate;

    /**
     * @ORM\Column(name="till_date", type="datetime")
     */
    protected $tillDate;

    /**
     * @var datetime $registration
     *
     * @ORM\Column(name="registration", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Pill
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Pill
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set repeat
     *
     * @param string $repeat
     * @return Pill
     */
    public function setRepeat($repeat)
    {
        $this->repeat = $repeat;

        return $this;
    }

    /**
     * Get repeat
     *
     * @return string 
     */
    public function getRepeat()
    {
        return $this->repeat;
    }

    /**
     * Set sinceDate
     *
     * @param \DateTime $sinceDate
     * @return Pill
     */
    public function setSinceDate($sinceDate)
    {
        if (is_string($sinceDate)) {
            $this->sinceDate = new \DateTime($sinceDate);
        }

        return $this;
    }

    /**
     * Get sinceDate
     *
     * @return \DateTime 
     */
    public function getSinceDate()
    {
        return $this->sinceDate;
    }

    /**
     * Set tillDate
     *
     * @param \DateTime $tillDate
     * @return Pill
     */
    public function setTillDate($tillDate)
    {
        if (is_string($tillDate)) {
            $this->tillDate = new \DateTime($tillDate);
        }

        return $this;
    }

    /**
     * Get tillDate
     *
     * @return \DateTime 
     */
    public function getTillDate()
    {
        return $this->tillDate;
    }

    /**
     * Set createdAt
     *
     * @ORM\PrePersist()
     *
     * @return Pill
     */
    public function setCreatedAt()
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Pill
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

    /**
     * Set user
     *
     * @param \Engage360d\Bundle\TakedaBundle\Entity\User\User $user
     * @return Pill
     */
    public function setUser(\Engage360d\Bundle\TakedaBundle\Entity\User\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Engage360d\Bundle\TakedaBundle\Entity\User\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Pill
     */
    public function setTime($time)
    {
        if (is_string($time)) {
            $this->time = new \DateTime($time);
        }

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }
}
