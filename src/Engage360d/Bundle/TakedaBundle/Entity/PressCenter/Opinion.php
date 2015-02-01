<?php

namespace Engage360d\Bundle\TakedaBundle\Entity\PressCenter;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="OpinionRepository")
 */
class Opinion extends Article
{
    const REPOSITORY = 'Engage360dTakedaBundle:PressCenter\Opinion';

    /**
     * @ORM\ManyToOne(targetEntity="Expert")
     * @ORM\JoinColumn(name="expert_id", referencedColumnName="id")
     **/
    protected $expert;

    public function getType()
    {
        return self::TYPE_OPINION;
    }

    /**
     * Set expert
     *
     * @param \Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert $expert
     * @return Opinion
     */
    public function setExpert(\Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert $expert = null)
    {
        $this->expert = $expert;

        return $this;
    }

    /**
     * Get expert
     *
     * @return \Engage360d\Bundle\TakedaBundle\Entity\PressCenter\Expert 
     */
    public function getExpert()
    {
        return $this->expert;
    }
}
