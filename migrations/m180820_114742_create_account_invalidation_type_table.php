<?php

use yii\db\Migration;

/**
 * Handles the creation of table `account_invalidation_type`.
 */
class m180820_114742_create_account_invalidation_type_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('account_invalidation_type', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->insert('account_invalidation_type', [
            'id' => 1,
            'name' => 'Is private',
        ]);
        $this->insert('account_invalidation_type', [
            'id' => 2,
            'name' => 'Not found',
        ]);

        $this->addColumn('account', 'invalidation_type_id', $this->integer());
        $this->addForeignKey('fk_account_invalidation_type', 'account', 'invalidation_type_id', 'account_invalidation_type', 'id', 'SET NULL');

        $this->addColumn('account', 'invalidation_count', $this->integer());
        $this->addColumn('account', 'update_stats_after', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_account_invalidation_type', 'account');
        $this->dropColumn('account', 'invalidation_type_id');
        $this->dropTable('account_invalidation_type');

        $this->dropColumn('account', 'invalidation_count');
        $this->dropColumn('account', 'update_stats_after');
    }
}
