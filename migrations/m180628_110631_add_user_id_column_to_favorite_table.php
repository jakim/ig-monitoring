<?php

use yii\db\Migration;

/**
 * Handles adding user_id to table `favorite`.
 */
class m180628_110631_add_user_id_column_to_favorite_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('favorite', 'user_id', $this->integer());
        $this->addForeignKey('fk_favorite_user', 'favorite', 'user_id', 'user', 'id', 'CASCADE');

        $this->execute('INSERT INTO favorite (`user_id`, `label`, `url`, `created_at`) SELECT `user`.id, `label`, `url`, `favorite`.`created_at` FROM `user`, `favorite`');
        $this->delete('favorite', ['user_id' => null]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_favorite_user', 'favorite');
        $this->dropColumn('favorite', 'user_id');
    }
}
