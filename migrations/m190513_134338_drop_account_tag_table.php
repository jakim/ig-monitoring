<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%account_tag}}`.
 */
class m190513_134338_drop_account_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk-account_tag-account_id', 'account_tag');
        $this->dropForeignKey('fk-account_tag-tag_id', 'account_tag');
        $this->dropForeignKey('fk_account_tag_user', 'account_tag');
        $this->dropTable('account_tag');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey('fk-account_tag-account_id', 'account_tag', 'account_id', 'account', 'id', 'CASCADE');
        $this->addForeignKey('fk-account_tag-tag_id', 'account_tag', 'tag_id', 'tag', 'id', 'CASCADE');
        $this->addForeignKey('fk_account_tag_user', 'account_tag', 'user_id', 'user', 'id', 'CASCADE');
        $this->createTable('account_tag', [
            'id' => $this->primaryKey(),
        ]);
    }
}
