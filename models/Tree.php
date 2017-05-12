<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Tree extends Model
{
    public $count;

    public function rules()
    {
        return [
            ['count', 'required'],
            ['count', 'integer']
        ];
    }

    
    public function generate($id)
    {
        if ($this->validate()) {
            
            $sql_update = "UPDATE tree SET right_key = right_key + 2, left_key = IF(left_key > :right_key, left_key + 2, left_key) WHERE right_key >= :right_key";

            $sql_insert = "INSERT INTO tree SET left_key = :right_key, right_key = :right_key + 1, level = :level + 1, name = ''";
            
            for( $i = 0; $i < $this->count; $i++) {

                $parent = Yii::$app->db->createCommand("SELECT level, right_key FROM tree WHERE id=$id")
                ->queryOne();
                
                Yii::$app->db->transaction(function($db) use ($sql_update, $sql_insert, $parent) {
                    $db->createCommand($sql_update)
                        ->bindValue(':right_key', $parent['right_key'])
                        ->execute();
                    $db->createCommand($sql_insert)
                        ->bindValues([':right_key' =>  $parent['right_key'], ':level' => $parent['level']])
                        ->execute();
                });
            }

            return true;
        }
        return false;
    }
}
