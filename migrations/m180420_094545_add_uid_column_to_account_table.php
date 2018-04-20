<?php

use yii\db\Migration;

/**
 * Handles adding uid to table `account`.
 */
class m180420_094545_add_uid_column_to_account_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'uid', $this->string()->after('id'));

        $accountIds = (new \yii\db\Query())
            ->from('account')
            ->andWhere(['account.monitoring' => 1])
            ->column();

        foreach ($accountIds as $accountId) {

            do {
                $uid = Yii::$app->security->generateRandomString(64);
                $uidExist = (new \yii\db\Query())
                    ->from('account')
                    ->andWhere(['account.uid' => $uid])
                    ->exists();
            } while ($uidExist);

            $this->update('account', ['uid' => $uid], ['id' => $accountId]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account', 'uid');
    }
}
