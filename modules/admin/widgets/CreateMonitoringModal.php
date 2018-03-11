<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 06.02.2018
 */

namespace app\modules\admin\widgets;


use app\components\ArrayHelper;
use app\models\Proxy;
use app\models\Tag;
use app\modules\admin\widgets\base\ModalWidget;
use kartik\select2\Select2;
use yii\helpers\Html;

class CreateMonitoringModal extends ModalWidget
{
    public $modalHeader = 'Create account monitoring';
    public $modalToggleButton = [
        'label' => 'Create',
    ];

    protected function renderModalContent()
    {
        echo Html::beginForm('create-account');

        echo "<div class=\"form-group\">";
        echo Html::input('text', 'username', null, [
            'placeholder' => 'Username',
            'class' => 'form-control',
            'required' => true,
            'autofocus' => true,
        ]);
        echo "</div>";

        echo "<div class='panel panel-default'>";
        echo "<div class='panel-heading'>Proxy settings</div>";
        echo "<div class='panel-body'>";
        echo "<div class=\"form-group\">";
        echo Select2::widget([
            'name' => 'proxy_id',
            'options' => [
                'placeholder' => 'Select dedicated proxy...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'data' => $this->getProxyPairs(),
        ]);
        echo "</div>";
        echo "<div class=\"form-group\">or</div>";

        echo "<div class=\"form-group\">";
        echo Select2::widget([
            'name' => 'proxy_tag_id',
            'options' => [
                'placeholder' => 'Select proxy tag...',
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
            'data' => $this->getProxyTagPairs(),
        ]);
        echo "<div class='help-block'>the proxy for the request will be randomly selected from the group</div>";
        echo "</div>";
        echo "<div class=\"form-group\">or</div>";
        echo "<div class=\"form-group alert alert-info\">leave empty if you want to use the default one</div>";

        echo "</div>";
        echo "</div>";

        echo Html::submitButton('Create', ['class' => 'btn btn-primary']);

        echo Html::endForm();
    }

    /**
     * @return array
     */
    protected function getProxyPairs(): array
    {
        return ArrayHelper::map(Proxy::find()->active()->all(), 'id', function(Proxy $model) {
            $tags = ArrayHelper::getColumn($model->tags, 'name');

            return $model->ip . ($tags ? ' # ' . implode(',', $tags) : '');
        });
    }

    /**
     * @return array
     */
    protected function getProxyTagPairs(): array
    {
        return Tag::find()
            ->indexBy('id')
            ->select('name')
            ->innerJoin('proxy_tag', 'tag.id=proxy_tag.tag_id')
            ->column();
    }
}