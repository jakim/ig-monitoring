<?php

use yii\db\Migration;

/**
 * Class m180622_040024_drop_account_columns_from_media
 */
class m180622_040024_drop_account_columns_from_media extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('media', 'account_followed_by');
        $this->dropColumn('media', 'account_follows');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('media', 'account_followed_by', $this->integer());
        $this->addColumn('media', 'account_follows', $this->integer());
    }
}
