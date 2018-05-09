<?php

use yii\db\Migration;

/**
 * Handles adding reservation_uid to table `proxy`.
 */
class m180509_091009_add_reservation_uid_column_to_proxy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('proxy', 'reservation_uid', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('proxy', 'reservation_uid');
    }
}
