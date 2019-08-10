<?php

use yii\db\Migration;

/**
 * Handles the creation of table `favorite`.
 */
class m180201_155243_create_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('favorite', [
            'id' => $this->primaryKey(),
            'label' => $this->string()->notNull()->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'),
            'url' => $this->string()->notNull(),
            'created_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('favorite');
    }
}
