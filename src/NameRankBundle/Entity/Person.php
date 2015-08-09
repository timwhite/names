<?php

namespace NameRankBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Person
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="Ranking", mappedBy="person")
     */
    private $rankings;


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
     * @return Person
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
     * Constructor
     */
    public function __construct()
    {
        $this->rankings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add rankings
     *
     * @param \NameRankBundle\Entity\Ranking $rankings
     * @return Person
     */
    public function addRanking(\NameRankBundle\Entity\Ranking $rankings)
    {
        $this->rankings[] = $rankings;
        $rankings->setPerson($this);

        return $this;
    }

    /**
     * Remove rankings
     *
     * @param \NameRankBundle\Entity\Ranking $rankings
     */
    public function removeRanking(\NameRankBundle\Entity\Ranking $rankings)
    {
        $this->rankings->removeElement($rankings);
    }

    /**
     * Get rankings
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRankings()
    {
        return $this->rankings;
    }
}
