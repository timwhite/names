<?php

/**
 * This class calculates ratings based on the Elo system used in chess.
 *
 * @author Michal Chovanec <michalchovaneceu@gmail.com>
 * @copyright Copyright © 2012 - 2014 Michal Chovanec
 * @license Creative Commons Attribution 4.0 International License
 */

namespace App\Rating;

class Rating
{

    /**
     * @var int The K Factor used.
     */
    const KFACTOR = 16;

    protected $_expectedA;
    protected $_expectedB;

    protected $_newRatingA;
    protected $_newRatingB;

    /**
     * Constructor function which does all the maths and stores the results ready
     * for retrieval.
     *
     * @param int $_ratingA Current rating of A
     * @param int $_ratingB Current rating of B
     * @param int $_scoreA Score of A
     * @param int $_scoreB Score of B
     */
    public function __construct(
        protected int $_ratingA, protected int $_ratingB, protected int $_scoreA, protected int $_scoreB)
    {
        $expectedScores = $this->_getExpectedScores($this->_ratingA, $this->_ratingB);
        $this->_expectedA = $expectedScores['a'];
        $this->_expectedB = $expectedScores['b'];

        $newRatings = $this->_getNewRatings($this->_ratingA, $this->_ratingB, $this->_expectedA, $this->_expectedB, $this->_scoreA, $this->_scoreB);
        $this->_newRatingA = $newRatings['a'];
        $this->_newRatingB = $newRatings['b'];
    }

    /**
     * Set new input data.
     *
     * @param int Current rating of A
     * @param int Current rating of B
     * @param int Score of A
     * @param int Score of B
     */
    public function setNewSettings($ratingA, $ratingB, $scoreA, $scoreB)
    {
        $this->_ratingA = $ratingA;
        $this->_ratingB = $ratingB;
        $this->_scoreA = $scoreA;
        $this->_scoreB = $scoreB;

        $expectedScores = $this->_getExpectedScores($this->_ratingA, $this->_ratingB);
        $this->_expectedA = $expectedScores['a'];
        $this->_expectedB = $expectedScores['b'];

        $newRatings = $this->_getNewRatings($this->_ratingA, $this->_ratingB, $this->_expectedA, $this->_expectedB, $this->_scoreA, $this->_scoreB);
        $this->_newRatingA = $newRatings['a'];
        $this->_newRatingB = $newRatings['b'];
    }

    /**
     * Retrieve the calculated data.
     *
     * @return Array An array containing the new ratings for A and B.
     */
    public function getNewRatings()
    {
        return ['a' => $this->_newRatingA, 'b' => $this->_newRatingB];
    }

    /**
     * Protected & private functions begin here
     */

    protected function _getExpectedScores($ratingA, $ratingB)
    {
        $expectedScoreA = 1 / (1 + (10 ** (($ratingB - $ratingA) / 400)));
        $expectedScoreB = 1 / (1 + (10 ** (($ratingA - $ratingB) / 400)));

        return ['a' => $expectedScoreA, 'b' => $expectedScoreB];
    }

    protected function _getNewRatings($ratingA, $ratingB, $expectedA, $expectedB, $scoreA, $scoreB)
    {
        $newRatingA = $ratingA + (self::KFACTOR * ($scoreA - $expectedA));
        $newRatingB = $ratingB + (self::KFACTOR * ($scoreB - $expectedB));

        return ['a' => $newRatingA, 'b' => $newRatingB];
    }

}
