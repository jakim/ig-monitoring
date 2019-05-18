<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-26
 */

namespace app\components\visualizations\traits;


use app\dictionaries\Color;
use app\models\Account;
use yii\base\InvalidConfigException;

trait AccountDataProviderTrait
{
    public $colors = [
        'followed_by' => Color::PRIMARY,
        'follows' => Color::PURPLE,
        'media' => Color::ORANGE,
        'er' => Color::SUCCESS,
        'avg_likes' => Color::INFO,
        'avg_comments' => Color::TEAL,
    ];

    public $labelFormat = 'date';

    public $dataSetsConfig = [];

    public $scalesConfig = [];

    /**
     * @var \app\models\Account
     */
    public $account;

    public function setAccount(Account $account)
    {
        $this->account = $account;

        return $this;
    }

    protected function throwExceptionIfAccountIsNotSet()
    {
        if (!$this->account) {
            throw new InvalidConfigException('Property \'account\' can not be empty.');
        }
    }

    private $labels;

    private $dataSets;

    private $scales;

    public function labels()
    {
        if (!$this->labels) {
            $this->labels = $this->prepareLabels();
        }

        return $this->labels;
    }

    public function dataSets()
    {
        if (!$this->dataSets) {
            $this->dataSets = $this->prepareDataSets();
        }

        return $this->dataSets;
    }

    public function scales()
    {
        if (!$this->scales) {
            $this->scales = $this->prepareScales();
        }

        return $this->scales;
    }

    abstract protected function prepareLabels(): array;

    abstract protected function prepareDataSets(): array;

    abstract protected function prepareScales(): array;
}