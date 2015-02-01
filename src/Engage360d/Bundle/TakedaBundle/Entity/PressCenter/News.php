<?php

namespace Engage360d\Bundle\TakedaBundle\Entity\PressCenter;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="NewsRepository")
 */
class News extends Article
{
    const REPOSITORY = 'Engage360dTakedaBundle:PressCenter\News';

    /**
     * @ORM\ManyToOne(targetEntity="Melodia\CatalogBundle\Entity\Record")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     **/
    protected $category;

    public function getType()
    {
        return self::TYPE_NEWS;
    }

    /**
     * Set category
     *
     * @param \Melodia\CatalogBundle\Entity\Record $category
     * @return News
     */
    public function setCategory(\Melodia\CatalogBundle\Entity\Record $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Melodia\CatalogBundle\Entity\Record 
     */
    public function getCategory()
    {
        return $this->category;
    }
}
