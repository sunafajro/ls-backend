<?php

namespace api\modules\client\models;

use api\modules\client\models\queries\NewsQuery;
use client\models\File;
use client\models\Message;
use yii\helpers\Url;

/**
 * Class News
 * @package api\modules\client\models
 */
class News extends Message
{
    /**
     * {@inheritdoc}
     */
    public function fields(): array
    {
        return [
            'id',
            'subject' => 'name',
            'body' => 'description',
            'image' => 'image',
            'attachments' => 'attachments',
            'createdAt' => 'dateFormatted',
        ];
    }

    /**
     * @return NewsQuery
     */
    public static function find(): NewsQuery
    {
        $query = new NewsQuery(get_called_class(), []);
        return $query->where(['calc_messwhomtype' => Message::TARGET_ALL_STUDENTS])
            ->active()
            ->sent();
    }

    /**
     * @return false|string
     */
    public function getDateFormatted()
    {
        return date('d.m.Y', strtotime($this->data));
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        if (!empty($this->files)) {
            $addr = explode('|', $this->files);
            if (!empty($addr[0])) {
                $ext = explode('.', $addr[0]);
                if (in_array($ext[1], ['jpg', 'jpeg', 'png', 'bmp', 'gif'])) {
                    return "https://student.language-school.ru/uploads/calc_message/{$this->id}/fls/{$addr[0]}";
                }
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getAttachments(): array
    {
        /** @var File[] $files */
        $files = File::find()->andWhere([
            'entity_type' => File::TYPE_MESSAGE_FILES, 'entity_id' => $this->id
        ])->all();

        return array_map(function(File $item) {
            return [
                'url' => Url::to(["files/{$item->id}"]),
                'name' => $item->original_name,
            ];
        }, $files);
    }
}