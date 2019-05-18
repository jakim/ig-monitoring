<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 17.02.2018
 */

namespace app\modules\admin\widgets;


use Yii;
use yii\base\Widget;

class ChangeInfoBox extends Widget
{
    public $header = 'Bookmarks';
    public $number = '41';
    public $format = 'integer';

    protected $bgCssClass = 'bg-blue';

    /**
     * @var \app\components\Formatter
     */
    protected $formatter;

    public function init()
    {
        $this->formatter = Yii::$app->formatter;
        parent::init();
        $this->number = $this->formatter->asChange($this->number, false, $this->format);
        if ($this->number > 0) {
            $this->bgCssClass = 'bg-green';
        } elseif ($this->number < 0) {
            $this->bgCssClass = 'bg-red';
        }
    }

    public function run()
    {
        echo "<div class=\"small-box {$this->bgCssClass}\">";
        echo "<div class=\"inner\">";
        echo "<span class=\"info-box-text\">{$this->header}</span>";
        echo "<span class=\"info-box-number\">{$this->number}</span>";
        echo "</div>";
        echo "</div>";
    }
}