<?php

/**
 * Class StatModel
 * Статистика
 */
class StatModel
{
    private $requests;
    private $doctors;
    private $reviews;

    /**
     * @return int
     */
    public function getDoctors()
    {
        return $this->doctors;
    }

    /**
     * @param int $doctors
     */
    public function setDoctors($doctors)
    {
        $this->doctors = $doctors;
    }

    /**
     * @return int
     */
    public function getRequests()
    {
        return $this->requests;
    }

    /**
     * @param int $requests
     */
    public function setRequests($requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return int
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param int $reviews
     */
    public function setReviews($reviews)
    {
        $this->reviews = $reviews;
    }
}