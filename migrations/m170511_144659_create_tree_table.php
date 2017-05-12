<?php

use yii\db\Migration;

/**
 * Handles the creation of table `tree`.
 */
class m170511_144659_create_tree_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tree', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'left_key' => $this->integer()->notNull(),
            'right_key' => $this->integer()->notNull(),
            'level' => $this->integer()->notNull()
        ]);

        $this->insert('tree', [
            'name' => 1,
            'level' => 1,
            'left_key' => 1,
            'right_key' => 2
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tree');
    }
}
