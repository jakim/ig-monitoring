<?php

use yii\db\Migration;

/**
 * Handles adding user_id to table `account_tag`.
 */
class m180628_113634_add_user_id_column_to_account_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropPrimaryKey('PRIMARY', 'account_tag');
        $this->addColumn('account_tag', 'user_id', $this->integer());
        $this->addForeignKey('fk_account_tag_user', 'account_tag', 'user_id', 'user', 'id', 'CASCADE');
        $this->execute('INSERT INTO account_tag (`user_id`, `account_id`, `tag_id`, `created_at`) SELECT `user`.id, `account_id`, `tag_id`, `account_tag`.`created_at` FROM `user`, `account_tag`');
        $this->delete('account_tag', ['user_id' => null]);
        $this->addPrimaryKey('idx_primary', 'account_tag', ['account_id', 'tag_id', 'user_id']);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('PRIMARY', 'account_tag');
        $this->dropForeignKey('fk_account_tag_user', 'account_tag');
        $this->dropColumn('account_tag', 'user_id');
        $this->delete('account_tag');
        $this->addPrimaryKey('idx_primary', 'account_tag', ['account_id', 'tag_id']);
    }
}
