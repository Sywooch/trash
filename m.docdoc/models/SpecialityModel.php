<?php

/**
 * Class StatModel
 * Специальности
 */
class SpecialityModel
{

	/**
	 * Идентификатор
	 *
	 * @var integer
	 */
    private $id;

	/**
	 * Название
	 *
	 * @var string
	 */
    private $name;

	/**
	 * Название в родительном падеже
	 *
	 * @var string
	 */
	private $_nameGenitive;

	/**
	 * Название во множественном числе
	 *
	 * @var string
	 */
	private $_namePlural;

	/**
	 * Название в родительном падеже во множественном числе
	 *
	 * @var string
	 */
	private $_namePluralGenitive;

	/**
	 * Абривиатура URL
	 *
	 * @var string
	 */
    private $alias;

	/**
	 * Уникальная ли специальность
	 *
	 * @var bool
	 */
	private $_isSimple = true;

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
	 * Получает название в родительном падеже
	 *
	 * @return string
	 */
	public function getNameGenitive()
	{
		return $this->_nameGenitive;
	}

	/**
	 * Устанавливает название в родительном падеже
	 *
	 * @param string $nameGenitive
	 */
	public function setNameGenitive($nameGenitive)
	{
		$this->_nameGenitive = $nameGenitive;
	}

	/**
	 * Получает название во множественном числе
	 *
	 * @return string
	 */
	public function getNamePlural()
	{
		return $this->_namePlural;
	}

	/**
	 * Устанавливает название во множественном числе
	 *
	 * @param string $namePlural
	 */
	public function setNamePlural($namePlural)
	{
		$this->_namePlural = $namePlural;
	}

	/**
	 * Получает название в родительном падеже во множественном числе
	 *
	 * @return string
	 */
	public function getNamePluralGenitive()
	{
		return $this->_namePluralGenitive;
	}

	/**
	 * Устанавливает название в родительном падеже во множественном числе
	 *
	 * @param string $namePluralGenitive
	 */
	public function setNamePluralGenitive($namePluralGenitive)
	{
		$this->_namePluralGenitive = $namePluralGenitive;
	}

	/**
	 * Устанавливает уникальность специальности
	 *
	 * @param bool $isSimple
	 *
	 * @return void
	 */
	public function setSimple($isSimple)
	{
		$this->_isSimple = boolval($isSimple);
	}

	/**
	 * Определяет уникальность специальности
	 *
	 * @return bool
	 */
	public function isSimple()
	{
		return $this->_isSimple;
	}
}