<?php

namespace app\models;

use Yii;
use yii\web\ServerErrorHttpException;

/**
 * This is the model class for table "group_books".
 *
 * @property integer $id
 * @property integer $book_id
 * @property integer $group_id
 * @property integer $primary
 */
class GroupBook extends \yii\db\ActiveRecord
{
    const TYPE_PRIMARY = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_books';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['book_id', 'group_id'], 'required'],
            [['book_id', 'group_id', 'primary'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'       => Yii::t('app', 'ID'),
            'book_id'  => Yii::t('app', 'Book'),
            'group_id' => Yii::t('app', 'Group'),
            'primary'  => Yii::t('app', 'Primary'),
        ];
    }

    // Сохранение с заменой основного учебника
    public function saveWithPrimaryCheck()
    {
        $isPrimary = (int)$this->primary;
        if ($isPrimary) {
            $this->primary = 0;
        }
        if ($this->save()) {
            return $isPrimary ? static::setPrimary($this) : true;
        } else {
            return false;
        }
    }

    /**
     * Возвращает основной учебник группы
     * @param int $groupId
     * 
     * @return GroupBook|null
     */
    public static function getPrimary($groupId)
    {
        return self::find()->andWhere(['primary' => $groupId, 'primary' => self::TYPE_PRIMARY])->one();
    }

    /**
     * Задает основной учебник для группы (снимает отметку предыдущего основного учебника)
     * @param GroupBook $groupBook
     * 
     * @return bool
     */
    public static function setPrimary(GroupBook $groupBook) : bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $previousPrimary = self::getPrimary($groupBook->group_id);
            if (!empty($previousPrimary)) {
                $previousPrimary->primary = 0;
                if (!$previousPrimary->save(true, ['primary'])) {
                    throw new ServerErrorHttpException('Error on unsetting primary group book.');
                }
            }
            $groupBook->primary = 1;
            if (!$groupBook->save(true, ['primary'])) {
                throw new ServerErrorHttpException('Error on setting primary group book.');
            }
            $transaction->commit();

            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }
    }

    public static function getBookListByGroup(int $groupId)
    {
        return self::find()->andWhere(['group_id' => $groupId])->all();
    }
}
