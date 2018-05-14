<?php

use yii\db\Migration;

/**
 * Handles dropping monitoring from table `media`.
 */
class m180513_095008_drop_monitoring_column_from_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('media', 'monitoring');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('media', 'monitoring', $this->boolean());
    }
}
