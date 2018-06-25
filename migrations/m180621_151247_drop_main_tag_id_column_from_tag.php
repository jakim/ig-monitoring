<?php

use yii\db\Migration;

/**
 * Class m180621_151247_drop_main_tag_id_column_from_tag
 */
class m180621_151247_drop_main_tag_id_column_from_tag extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('fk_tag_tag', 'tag');
        $this->dropColumn('tag', 'main_tag_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('tag', 'main_tag_id', $this->integer());
        $this->addForeignKey('fk_tag_tag', 'tag', 'main_tag_id', 'tag', 'id');
    }
}
