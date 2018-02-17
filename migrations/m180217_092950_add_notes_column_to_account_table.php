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
        $this->addColumn('account', 'notes', $this->string());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('account', 'notes');
    }
}
