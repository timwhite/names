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
     * @ORM\Column(name="is_male", type="integer", nullable=False)
     */
    private $is_male = True;

    /**
     * @ORM\OneToMany(targetEntity="Ranking", mappedBy="name")
     */
    private $ranking;


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
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ranking = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add ranking
     *
     * @param \NameRankBundle\Entity\Ranking $ranking
     * @return Name
     */
    public function addRanking(\NameRankBundle\Entity\Ranking $ranking)
    {
        $this->ranking[] = $ranking;
        $ranking->setName($this);

        return $this;
    }

    /**
     * Get ranking
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRanking()
    {
        return $this->ranking;
    }

    /**
     * Get overall ranking
     */
    public function getRank()
    {
        $overallrank =0;
        foreach($this->getRanking() as $rank)
        {
            $overallrank += $rank->getRank();
        }
        return $overallrank;
    }
}
