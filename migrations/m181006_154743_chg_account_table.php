<?php

use yii\db\Migration;

/**
 * Class m181006_154743_chg_account_table
 */
class m181006_154743_chg_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'followed_by', $this->integer());
        $this->addColumn('account', 'follows', $this->integer());
        $this->addColumn('account', 'media', $this->integer());
        $this->addColumn('account', 'er', $this->money());
        $this->addColumn('account', 'avg_likes', $this->money());
        $this->addColumn('account', 'avg_comments', $this->money());
        $this->addColumn('account', 'stats_updated_at', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'followed_by');
        $this->dropColumn('account', 'follows');
        $this->dropColumn('account', 'media');
        $this->dropColumn('account', 'er');
        $this->dropColumn('account', 'avg_likes');
        $this->dropColumn('account', 'avg_comments');
        $this->dropColumn('account', 'stats_updated_at');
    }
}
