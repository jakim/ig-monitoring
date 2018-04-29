<?php

use yii\db\Migration;

/**
 * Class m180429_160926_fix_public_link
 */
class m180429_160926_fix_public_link extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $accountIds = (new \yii\db\Query())
            ->from('account')
            ->andWhere(['account.monitoring' => 1])
            ->andWhere(['account.uid' => null])
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
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180429_160926_fix_public_link cannot be reverted.\n";

        return false;
    }
    */
}
