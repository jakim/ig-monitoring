<?php

use yii\db\Migration;

/**
 * Class m180217_085807_chg_account_stats_table
 */
class m180217_085807_chg_account_stats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('account_stats', 'er', $this->decimal(8, 4));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
