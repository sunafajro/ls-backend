<?php

namespace app\commands;

use Yii;
use app\models\Book;
use yii\console\Controller;

class BookController extends Controller
{
    public function actionPrepare()
    {
        $books = (new \yii\db\Query())
        ->select([
            'name'        => 'b.name',
            'author'      => 'b.author',
            'isbn'        => 'b.isbn',
            'description' => 'b.description',
            'publisher'   => 'bp.name',
            'language_id' => 'b.calc_lang',
            'user_id'     => 'b.user',
            'created_at'  => 'b.data',
        ])
        ->from(['b' => 'calc_book'])
        ->innerJoin(['bp' => 'calc_bookpublisher'], 'bp.id = b.calc_bookpublisher')
        ->where(['b.visible' => 1])
        ->orderBy(['b.id' => SORT_ASC])
        ->all();

        $transaction = Yii::$app->db->beginTransaction();
        try {
            foreach ($books as $book) {
                $newBook              = new Book();
                $newBook->name        = $book['name'];
                $newBook->author      = $book['author'];
                $newBook->isbn        = $book['isbn'];
                $newBook->description = $book['description'];
                $newBook->publisher   = $book['publisher'];
                $newBook->language_id = (int)$book['language_id'];
                $newBook->user_id     = (int)$book['user_id'];
                $newBook->created_at  = substr($book['created_at'], 0, 10);
                if (!$newBook->save()) {
                    throw new Exception('During book preparation an error occurs!');
                }
            }
            $transaction->commit();
            echo 'Books data successfully prepared!';
        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getMessage();
        }
    }
}