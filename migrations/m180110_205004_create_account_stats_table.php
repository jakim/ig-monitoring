<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account_stats`.
 */
class m180110_205004_create_account_stats_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('account_stats', [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'followed_by' => $this->integer(),
            'follows' => $this->integer(),
            'media' => $this->integer(),
            'created_at' => $this->dateTime(),
        ]);
        $this->addForeignKey('fk_account_stats_account', 'account_stats', 'account_id', 'account', 'id', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk_account_stats_account', 'account_stats');
        $this->dropTable('account_stats');
    }
}
