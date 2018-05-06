<?php

use yii\db\Migration;

/**
 * Handles adding access_token to table `user`.
 */
class m180506_180112_add_access_token_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'access_token', $this->string());

        $userIds = (new \yii\db\Query())
            ->from('user')
            ->column();

        foreach ($userIds as $userId) {

            do {
                $token = Yii::$app->security->generateRandomString(64);
                $tokenExist = (new \yii\db\Query())
                    ->from('user')
                    ->andWhere(['user.access_token' => $token])
                    ->exists();
            } while ($tokenExist);

            $this->update('user', ['access_token' => $token], ['id' => $userId]);
        }

        $this->alterColumn('user', 'access_token', $this->string()->notNull()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'access_token');
    }
}
