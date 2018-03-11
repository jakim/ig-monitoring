<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\models\Account;
use app\models\Proxy;
use app\models\Tag;
use app\modules\admin\models\AccountMonitoringForm;
use app\modules\admin\widgets\base\ModalWidget;
use kartik\select2\Select2;
use yii\helpers\Html;

class CreateMonitoringModal extends ModalWidget
{
    /**
     * @var AccountMonitoringForm
     */
    public $form;
    public $modalHeader = 'Create monitoring';
    public $modalToggleButton = [
        'label' => 'Create',
    ];

    protected function renderModalContent()
    {
        echo $this->render('create-account-monitoring', [
            'model' => $this->form ?: new AccountMonitoringForm(),
            'accountTags' => $this->getAccountTagPairs(),
            'proxies' => $this->getProxyPairs(),
            'proxyTags' => $this->getProxyTagPairs(),
        ]);
    }

    /**
     * @return array
     */
    protected function getProxyPairs(): array
    {
        return ArrayHelper::map(Proxy::find()->active()->all(), 'id', function (Proxy $model) {
            $tags = ArrayHelper::getColumn($model->tags, 'name');

            return $model->ip . ($tags ? ' # ' . implode(',', $tags) : '');
        });
    }

    /**
     * @return array
     */
    protected function getProxyTagPairs(): array
    {
        return ArrayHelper::map(Proxy::usedTags(), 'id', 'name');
    }

    protected function getAccountTagPairs()
    {
        return ArrayHelper::map(Account::usedTags(), 'name', 'name');
    }
}