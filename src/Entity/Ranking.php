<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ranking
 */
#[ORM\Table]
#[ORM\Entity]
class Ranking
{
    /**
     * @var integer
     */
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'rank', type: 'integer')]
    private $rank = 1000;

    /**
     * @var integer
     */
    #[ORM\Column(name: 'numberOfComparisons', type: 'integer')]
    private $numberOfComparisons = 0;

    #[ORM\JoinColumn(name: 'name_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Name::class, inversedBy: 'ranking')]
    private $name;

    #[ORM\JoinColumn(name: 'person_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Person::class)]
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
     * @param \App\Entity\Name $name
     * @return Ranking
     */
    public function setName(\App\Entity\Name $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return \App\Entity\Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set person
     *
     * @param \App\Entity\Person $person
     * @return Ranking
     */
    public function setPerson(\App\Entity\Person $person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get person
     *
     * @return \App\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }
}
