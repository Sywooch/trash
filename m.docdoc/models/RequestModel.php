<?php

/**
 * Class RequestModel
 * Записаться к врачу
 */
class RequestModel
{
    private $name;
    private $phone;
    private $doctor;
    private $comment;
    private $clinic;
    //доступно только после отправки заявки
    private $status;
    private $message;

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return int
     */
    public function getClinic()
    {
        return $this->clinic;
    }

    /**
     * @param int $clinic
     */
    public function setClinic($clinic)
    {
        $this->clinic = $clinic;
    }

    /**
     * @return int
     */
    public function getDoctor()
    {
        return $this->doctor;
    }

    /**
     * @param int $doctor
     */
    public function setDoctor($doctor)
    {
        $this->doctor = $doctor;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }
    
}