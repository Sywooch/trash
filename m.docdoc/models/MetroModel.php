<?php

/**
 * Class MetroModel
 * Метро
 */
class MetroModel
{
    private $id;
    private $name;
    private $lineName;
    private $lineColor;
    private $cityId;
    private $alias;

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getCityId()
    {
        return $this->cityId;
    }

    /**
     * @param int $cityId
     */
    public function setCityId($cityId)
    {
        $this->cityId = $cityId;
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
     * @return string
     */
    public function getLineColor()
    {
        return $this->lineColor;
    }

    /**
     * @param string $LineColor
     */
    public function setLineColor($LineColor)
    {
        $this->lineColor = $LineColor;
    }

    /**
     * @return string
     */
    public function getLineName()
    {
        return $this->lineName;
    }

    /**
     * @param string $LineName
     */
    public function setLineName($LineName)
    {
        $this->lineName = $LineName;
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
}