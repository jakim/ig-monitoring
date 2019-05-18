<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-02-25
 */

namespace app\modules\admin\components\grid;


class GridView extends \yii\grid\GridView
{
    public $layout = "{items}\n{pager}";

    public function renderPager()
    {
        return sprintf('<div class=""><div class="col-lg-5 ">%s</div><div class="col-lg-7 pull-right text-right">%s</div></div>',
            $this->renderSummary(),
            parent::renderPager()
        );
    }
}