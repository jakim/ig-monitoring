<?php

use yii\db\Migration;

/**
 * Class m180621_151103_chg_tag_add_indexes
 */
class m180621_151103_chg_tag_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('idx_tag_monitoring', 'tag', 'monitoring');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx_tag_monitoring', 'tag');
    }
}
