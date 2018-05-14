<?php

use yii\db\Migration;

/**
 * Handles dropping proxy_id from table `media`.
 */
class m180513_094658_drop_proxy_id_column_from_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_media_proxy', 'media');
        $this->dropColumn('media', 'proxy_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('media', 'proxy_id', $this->integer());
        $this->addForeignKey('fk_media_proxy', 'media', 'proxy_id', 'proxy', 'id', 'SET NULL');
    }
}
