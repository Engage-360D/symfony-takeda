<?php

namespace Engage360d\Bundle\TakedaBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="InstitutionRepository")
 * @ORM\Table(name="institutions", indexes={@ORM\Index(name="institution_parsed_town_index", columns={"parsedTown"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
class Institution
{
    const REPOSITORY = 'Engage360dTakedaBundle:Institution';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string")
     * @Serializer\Groups({"elastica"})
     */
    protected $name;

    /**
     * @ORM\Column(type="string")
     */
    protected $specialization;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Groups({"elastica"})
     */
    protected $address;

    /**
     * @ORM\Column(type="string")
     */
    protected $googleAddress;

    /**
     * @ORM\Column(type="string")
     */
    protected $region;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedTown;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedStreet;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedHouse;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedCorpus;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedBuilding;

    /**
     * @ORM\Column(type="string")
     */
    protected $parsedRegion;

    /**
     * @ORM\Column(type="float")
     */
    protected $lat;

    /**
     * @ORM\Column(type="float")
     */
    protected $lng;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Groups({"elastica"})
     */
    protected $priority;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    protected $deletedAt;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Region
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
     * Set specialization
     *
     * @param string $specialization
     * @return Institution
     */
    public function setSpecialization($specialization)
    {
        $this->specialization = $specialization;

        return $this;
    }

    /**
     * Get specialization
     *
     * @return string
     */
    public function getSpecialization()
    {
        return $this->specialization;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return Institution
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set googleAddress
     *
     * @param string $googleAddress
     * @return Institution
     */
    public function setGoogleAddress($googleAddress)
    {
        $this->googleAddress = $googleAddress;

        return $this;
    }

    /**
     * Get googleAddress
     *
     * @return string
     */
    public function getGoogleAddress()
    {
        return $this->googleAddress;
    }

    /**
     * Set region
     *
     * @param string $region
     * @return Institution
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * Get region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * Set parsedTown
     *
     * @param string $parsedTown
     * @return Institution
     */
    public function setParsedTown($parsedTown)
    {
        $this->parsedTown = $parsedTown;

        return $this;
    }

    /**
     * Get parsedTown
     *
     * @return string
     */
    public function getParsedTown()
    {
        return $this->parsedTown;
    }

    /**
     * Set parsedStreet
     *
     * @param string $parsedStreet
     * @return Institution
     */
    public function setParsedStreet($parsedStreet)
    {
        $this->parsedStreet = $parsedStreet;

        return $this;
    }

    /**
     * Get parsedStreet
     *
     * @return string
     */
    public function getParsedStreet()
    {
        return $this->parsedStreet;
    }

    /**
     * Set parsedHouse
     *
     * @param string $parsedHouse
     * @return Institution
     */
    public function setParsedHouse($parsedHouse)
    {
        $this->parsedHouse = $parsedHouse;

        return $this;
    }

    /**
     * Get parsedHouse
     *
     * @return string
     */
    public function getParsedHouse()
    {
        return $this->parsedHouse;
    }

    /**
     * Set parsedCorpus
     *
     * @param string $parsedCorpus
     * @return Institution
     */
    public function setParsedCorpus($parsedCorpus)
    {
        $this->parsedCorpus = $parsedCorpus;

        return $this;
    }

    /**
     * Get parsedCorpus
     *
     * @return string
     */
    public function getParsedCorpus()
    {
        return $this->parsedCorpus;
    }

    /**
     * Set parsedBuilding
     *
     * @param string $parsedBuilding
     * @return Institution
     */
    public function setParsedBuilding($parsedBuilding)
    {
        $this->parsedBuilding = $parsedBuilding;

        return $this;
    }

    /**
     * Get parsedBuilding
     *
     * @return string
     */
    public function getParsedBuilding()
    {
        return $this->parsedBuilding;
    }

    /**
     * Set parsedRegion
     *
     * @param string $parsedRegion
     * @return Institution
     */
    public function setParsedRegion($parsedRegion)
    {
        $this->parsedRegion = $parsedRegion;

        return $this;
    }

    /**
     * Get parsedRegion
     *
     * @return string
     */
    public function getParsedRegion()
    {
        return $this->parsedRegion;
    }

    public function getNormalizedAddress()
    {
        $parts = array(
            $this->getParsedTown(),
            $this->getParsedStreet(),
            $this->getParsedHouse(),
            $this->getParsedCorpus(),
            $this->getParsedBuilding(),
            $this->getParsedRegion(),
        );

        $parts = array_filter($parts, function ($part) { return strlen(trim($part)) > 0; });

        return implode(', ', $parts);
    }

    /**
     * Set priority
     *
     * @param integer $priority
     * @return Institution
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set lat
     *
     * @param float $lat
     * @return Institution
     */
    public function setLat($lat)
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * Get lat
     *
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * Set lng
     *
     * @param float $lng
     * @return Institution
     */
    public function setLng($lng)
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * Get lng
     *
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }
}
