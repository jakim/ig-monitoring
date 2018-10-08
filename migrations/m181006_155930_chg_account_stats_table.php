<?php

use yii\db\Migration;

/**
 * Class m181006_155930_chg_account_stats_table
 */
class m181006_155930_chg_account_stats_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account_stats', 'avg_likes', $this->money());
        $this->addColumn('account_stats', 'avg_comments', $this->money());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account_stats', 'avg_likes');
        $this->dropColumn('account_stats', 'avg_comments');
    }
}
