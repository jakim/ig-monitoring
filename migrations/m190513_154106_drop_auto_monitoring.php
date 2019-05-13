<?php

use yii\db\Migration;

/**
 * Class m190513_154106_drop_auto_monitoring
 */
class m190513_154106_drop_auto_monitoring extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('account', 'accounts_monitoring_level');
        $this->dropColumn('account', 'accounts_default_tags');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('account', 'accounts_monitoring_level', $this->integer());
        $this->addColumn('account', 'accounts_default_tags', $this->string());
    }
}
