<?php

namespace app\traits;

/**
 * Методы для объединения учетных записей студентов
 */
trait StudentMergeTrait {
    /**
     * метод подменяет в строках идентификатор одного студента на идентификатор другого
     * @param int $toId
     * @param int $fromId
     * @param string $column (default 'calc_studname')
     * @param array $params (default [])
     * @param string $table  (default null)
     * 
     * @return boolean
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