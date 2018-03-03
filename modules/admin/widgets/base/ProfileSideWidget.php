<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 03.03.2018
 */

namespace app\modules\admin\widgets\base;


abstract class ProfileSideWidget extends ModalWidget
{
    public $header = 'Side Widget';
    public $headerIcon = 'info';

    public function init()
    {
        $this->modalHeader = $this->header;
        parent::init();
    }

    public function run()
    {
        echo "<strong>";
        echo "<i class=\"fa fa-{$this->headerIcon} margin-r-5\"></i> {$this->header}";
        echo "</strong>";
        $this->renderModal();

        $this->renderBoxContent();
    }

    abstract protected function renderBoxContent();
}