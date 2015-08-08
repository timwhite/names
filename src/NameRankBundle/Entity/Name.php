<?php

namespace NameRankBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Name
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Name
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
     * @ORM\Column(name="name", type="string", length=255, nullable=False)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", nullable=False)
     */
    private $rank = 1000;

    /**
     * @var integer
     *
     * @ORM\Column(name="numberOfComparisons", type="integer", nullable=False)
     */
    private $numberOfComparisons = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="is_male", type="integer", nullable=False)
     */
    private $is_male = True;


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
     * @return Name
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
     * Set rank
     *
     * @param integer $rank
     * @return Name
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
     * @return Name
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
     * Set is_male
     *
     * @param integer $isMale
     * @return Name
     */
    public function setIsMale($isMale)
    {
        $this->is_male = $isMale;

        return $this;
    }

    /**
     * Get is_male
     *
     * @return integer 
     */
    public function getIsMale()
    {
        return $this->is_male;
    }
}
