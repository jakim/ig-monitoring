<?php

use yii\db\Migration;

/**
 * Handles adding notes to table `account`.
 */
class m180217_092950_add_notes_column_to_account_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('account', 'notes', $this->string()->append('CHARACTER SET utf8 COLLATE utf8_unicode_ci'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('account', 'notes');
    }
}
