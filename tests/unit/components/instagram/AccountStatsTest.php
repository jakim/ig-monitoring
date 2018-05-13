<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.05.2018
 */

use app\components\instagram\AccountDetails;
use app\components\instagram\AccountStats;

class AccountStatsTest extends \Codeception\Test\Unit
{

    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'account' => \app\tests\fixtures\AccountFixture::class,
            'account_stats' => \app\tests\fixtures\AccountStatsFixture::class,
            'media' => \app\tests\fixtures\MediaFixture::class,
        ]);
    }

    public function testUpdateStats()
    {
        $service = new AccountStats();
        /** @var \app\models\Account $account */
        $account = $this->tester->grabFixture('account', 'account1');
        $data = $this->tester->accountDummyData(5);

        $service->updateStats($account, $data);
        $this->tester->seeRecord(\app\models\AccountStats::class, [
            'media' => 5,
            'follows' => 5,
            'followed_by' => 5,
        ]);

        $data = $this->tester->accountDummyData(3);

        $service->updateStats($account, $data);
        $this->tester->seeRecord(\app\models\AccountStats::class, [
            'media' => 3,
            'follows' => 3,
            'followed_by' => 3,
        ]);

        $this->assertEquals(3, $account->lastAccountStats->media);
        $this->assertEquals(3, $account->lastAccountStats->follows);
        $this->assertEquals(3, $account->lastAccountStats->followed_by);
    }

    public function testUpdateEr()
    {
        $service = new AccountStats();
        /** @var \app\models\Account $account */
        $account = $this->tester->grabFixture('account', 'account1');
        $this->assertEquals(2, $account->lastAccountStats->followed_by);

        $post1 = $this->tester->postDummyData(1);
        $post2 = $this->tester->postDummyData(2);
        $post3 = $this->tester->postDummyData(5);

        $service2 = new AccountDetails();
        $service2->updateMedia($account, [$post1, $post2]);
        $service->updateEr($account);

        $account->refresh();
        $this->assertEquals(1.5, (float)$account->lastAccountStats->er);

        $service2->updateMedia($account, [$post1, $post2, $post3]);
        $account->refresh();
        $service->updateEr($account);
        $account->refresh();

        $this->assertEquals(2.6667, (float)$account->lastAccountStats->er);
    }

    public function testStatsNeedUpdate()
    {
        $service = new AccountStats();
        /** @var \app\models\Account $account */
        $account = $this->tester->grabFixture('account', 'account1');
        $data = $this->tester->accountDummyData();

        $this->assertTrue($service->statsNeedUpdate($account, $data));

        $data = $this->tester->accountDummyData(2);
        $this->assertFalse($service->statsNeedUpdate($account, $data));

        $data->media = 1;
        $this->assertTrue($service->statsNeedUpdate($account, $data));
    }
}
