<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-03-21
 */

namespace app\modules\admin\widgets;


use app\models\UserAccount;
use yii\base\Widget;

class AccountProfileBox extends Widget
{
    /**
     * @var \app\models\Account
     */
    public $model;

    public function run()
    {
        return $this->render('account-profile-box', [
            'model' => $this->model,
        ]);
    }
}