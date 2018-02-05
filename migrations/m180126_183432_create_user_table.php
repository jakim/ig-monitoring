<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180126_183432_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'image' => $this->string(),
            'google_user_id' => $this->string()->notNull(),
            'active' => $this->boolean()->notNull()->defaultValue(0),
            'updated_at' => $this->dateTime(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('user');
    }
}
