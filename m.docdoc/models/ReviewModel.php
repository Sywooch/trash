<?php

/**
 * Class ReviewModel
 * Отзывы
 */
class ReviewModel
{
    private $id;
    private $client;
    private $ratingQlf;
    private $ratingAtt;
    private $ratingRoom;
    private $text;
    private $date;
    private $doctorId;


    public static $ratingWords = [
        1 => 'Плохо.',
        2 => 'Неуд.',
        3 => 'Нормально.',
        4 => 'Хорошо.',
        5 => 'Отлично!',
    ];

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param string $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getDoctorId()
    {
        return $this->doctorId;
    }

    /**
     * @param int $doctorId
     */
    public function setDoctorId($doctorId)
    {
        $this->doctorId = $doctorId;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return float
     */
    public function getRatingAtt()
    {
        return $this->ratingAtt;
    }

    /**
     * @param float $ratingAtt
     */
    public function setRatingAtt($ratingAtt)
    {
        $this->ratingAtt = $ratingAtt;
    }

    /**
     * @return float
     */
    public function getRatingQlf()
    {
        return $this->ratingQlf;
    }

    /**
     * @param float $ratingQlf
     */
    public function setRatingQlf($ratingQlf)
    {
        $this->ratingQlf = $ratingQlf;
    }

    /**
     * @return float
     */
    public function getRatingRoom()
    {
        return $this->ratingRoom;
    }

    /**
     * @param float $ratingRoom
     */
    public function setRatingRoom($ratingRoom)
    {
        $this->ratingRoom = $ratingRoom;
    }

    /**
     * @return float
     */
    public function getSummaryRating()
    {
        return ($this->getRatingQlf() + $this->getRatingAtt() + $this->getRatingRoom()) / 3;
    }

    /**
     * Получение словесной оценки
     *
     * @return string
     */
    public function getRatingInWord()
    {
        $rating = round($this->getSummaryRating());

        if (isset(self::$ratingWords[$rating])) {
            return self::$ratingWords[$rating];
        }

        return "";
    }

}