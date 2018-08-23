<?php

use yii\db\Migration;

/**
 * Class m180823_132444_chg_account_disable_flag
 */
class m180823_132444_chg_account_disable_flag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->update('account', [
            'disabled' => 0,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }
}
