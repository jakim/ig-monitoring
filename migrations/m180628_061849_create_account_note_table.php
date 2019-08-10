<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account_note`.
 */
class m180628_061849_create_account_note_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account_note', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer(),
            'user_id' => $this->integer(),
            'note' => $this->string()->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'),
            'created_at' => $this->dateTime(),
        ]);

        $this->addForeignKey('fk_account_note_account', 'account_note', 'account_id', 'account', 'id', 'CASCADE');
        $this->addForeignKey('fk_account_note_user', 'account_note', 'user_id', 'user', 'id', 'CASCADE');

        $this->execute('INSERT INTO account_note (account_id, user_id, note, created_at) SELECT account.id, user.id, account.notes, DATE (NOW()) FROM `user`, account WHERE account.notes is not null');

        $this->dropColumn('account', 'notes');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_account_note_account', 'account_note');
        $this->dropForeignKey('fk_account_note_user', 'account_note');
        $this->dropTable('account_note');
        $this->addColumn('account', 'notes', $this->string());
    }
}
