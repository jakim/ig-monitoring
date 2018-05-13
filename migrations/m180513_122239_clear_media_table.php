<?php

use yii\db\Migration;

/**
 * Class m180513_122239_clear_media_table
 */
class m180513_122239_clear_media_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->delete('media', ['likes' => null]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180513_122239_clear_media_table cannot be reverted.\n";

        return false;
    }
    */
}
