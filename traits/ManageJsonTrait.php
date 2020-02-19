<?php

namespace app\traits;

use yii\helpers\Json;

/**
 * Методы для работы со свойствами модели в формате JSON
 */
trait ManageJsonTrait {
    /**
     * @param string $column
     * @param $defaultValue
     * 
     * @return mixed
     */
    public function getJsonColumn(string $column, $defaultValue = null)
    {
        $json = Json::decode($this->$column);

        return $json !== NULL ? $json : $defaultValue;
    }

    /**
     * @param string $column
     * @param string $property
     * @param $defaultValue
     * 
     * @return mixed
     */
    public function getJsonColumnProperty(string $column, string $property, $defaultValue = null)
    {
        $json = $this->getJsonColumn($column);

        return $json[$property] ?? $defaultValue; 
    }

    /**
     * @param string $column
     * @param $defaultValue
     */
    public function setJsonColumn(string $column, $value)
    {
        $this->$column = Json::encode($value);
    }

    /**
     * @param string $column
     * @param string $property
     * @param $defaultValue
     */
    public function setJsonColumnProperty(string $column, string $property, $value)
    {
        $json = $this->getJsonColumn($column);
        $json[$property] = $value;

        $this->setJsonColumn($column, $json);
    }
}