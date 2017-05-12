<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Tree;

class TreeController extends Controller
{
    public function actionIndex($id = 1, $ids = null)
    {
        $model = new Tree();

        if ($model->load(Yii::$app->request->post())) {
            if($model->generate($id)) {
                Yii::$app->session->setFlash('success', 'Дерево успешно сгенерировано');
            } else {
                Yii::$app->session->setFlash('error', 'Дерево не было сгенерировано :(');
            }
            
            return $this->refresh();
        }

        return $this->render('index', [
            'model' => $model,
            'id' => $ids ? $ids : $id,
        ]);
    }

    public function actionGetTree($id = 1, $ids = null)
    {
        $items = [];

        if($ids) {
            $ids = explode(',', $ids);
            foreach($ids as $id) {
                if($id) {
                    $items = array_merge($items, $this->getItems($id, true));
                }
            }

            $items = array_unique($items, SORT_REGULAR);
            $items = array_merge($items, array());

            $level = 1;
        } else {
            $item = Yii::$app->db->createCommand("SELECT level FROM tree WHERE id=$id")
                ->queryOne();
            $items = $this->getItems($id);

            $level = $item['level'];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return ['items' => $items, 'level' => $level];
    }

    private function getItems($id, $level = false)
    {
        $item = Yii::$app->db->createCommand("SELECT left_key, right_key, level FROM tree WHERE id=$id")
                ->queryOne();
                
        $values = [
                ':left_key' =>  $item['left_key'],
                ':right_key' => $item['right_key']
            ];

        if($level) {
            $query = 'SELECT * FROM tree WHERE right_key > :left_key AND left_key < :right_key ORDER BY left_key';
        } else {
            $query = 'SELECT * FROM tree WHERE right_key > :left_key AND left_key < :right_key AND level >= :level ORDER BY left_key';
            $values[':level'] = $item['level'];
        }
        

        $items = Yii::$app->db->createCommand($query)
            ->bindValues($values)
            ->queryAll();

        return $items;
    }

}