<?php

namespace NameRankBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ranking
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Ranking
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
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer")
     */
    private $rank = 1000;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfComparisons", type="integer")
     */
    private $numberOfComparisons = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Name", inversedBy="ranking")
     * @ORM\JoinColumn(name="name_id", referencedColumnName="id", nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=false)
     */
    private $person;



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
     * Set rank
     *
     * @param integer $rank
     * @return Ranking
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set numberOfComparisons
     *
     * @param integer $numberOfComparisons
     * @return Ranking
     */
    public function setNumberOfComparisons($numberOfComparisons)
    {
        $this->numberOfComparisons = $numberOfComparisons;

        return $this;
    }

    /**
     * Get numberOfComparisons
     *
     * @return integer 
     */
    public function getNumberOfComparisons()
    {
        return $this->numberOfComparisons;
    }

    public function incrementNumberOfComparisons()
    {
        $this->numberOfComparisons++;
    }

    /**
     * Set name
     *
     * @param \NameRankBundle\Entity\Name $name
     * @return Ranking
     */
    public function setName(\NameRankBundle\Entity\Name $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return \NameRankBundle\Entity\Name 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set person
     *
     * @param \NameRankBundle\Entity\Person $person
     * @return Ranking
     */
    public function setPerson(\NameRankBundle\Entity\Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \NameRankBundle\Entity\Person 
     */
    public function getPerson()
    {
        return $this->person;
    }
}
