<?php

namespace school\traits;

/**
 * Методы для объединения учетных записей студентов
 */
trait StudentMergeTrait {
    /**
     * @deprecated
     * // TODO переделать в менеджер (избавится от трейта), добавить перенос файлов (документы, вложения)
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param int         $toId
     * @param int         $fromId
     * @param string      $column
     * @param array       $params
     * @param string|null $table
     *
     * @return boolean
     * @throws \yii\db\Exception
     */
    public static function mergeStudents(int $toId, int $fromId, string $column = 'calc_studname', array $params = [], string $table = null) : bool
    {
        $tableName = $table ?? self::tableName();
        $where = array_merge([$column => $fromId], $params);

        $sql = (new \yii\db\Query())
        ->createCommand()
        ->update($tableName, [$column => $toId], $where)
        ->execute();

        return !($sql === 0);
    }
}