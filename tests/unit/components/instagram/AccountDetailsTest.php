<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 10.05.2018
 */

use app\components\instagram\AccountDetails;

class AccountDetailsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        $this->tester->haveFixtures([
            'account' => \app\tests\fixtures\AccountFixture::class,
            'media' => \app\tests\fixtures\MediaFixture::class,
        ]);
    }

    public function testUpdateDetails()
    {
        $service = new AccountDetails();

        $account = $this->tester->grabFixture('account', 'account1');
        $data = $this->tester->accountDummyData();

        $service->updateDetails($account, $data);

        $this->tester->seeRecord(\app\models\Account::class, [
            'username' => 'test_data1',
            'full_name' => 'test_data1',
            'biography' => 'test_data1',
            'external_url' => 'test_data1',
            'instagram_id' => 'test_data1',
        ]);
    }

    public function testProfilePicNeedUpdate()
    {
        $service = new AccountDetails();

        $account = $this->tester->grabFixture('account', 'account1');
        $data = $this->tester->accountDummyData();

        $this->assertTrue($service->profilePicNeedUpdate($account, $data));

        $filename = $service->profilePicFilename($account, $data);
        $path = $service->profilePicPath($filename);
        touch($path);

        $this->assertFalse($service->profilePicNeedUpdate($account, $data));

        unlink($path);
    }

    public function testUpdateProfilePic()
    {
        $service = new AccountDetails();

        $account = $this->tester->grabFixture('account', 'account1');
        $data = $this->tester->accountDummyData();
        $imgData = file_get_contents(codecept_data_dir('account_pic.jpg'));

        $service->updateProfilePic($account, $data, $imgData);

        $filename = $service->profilePicFilename($account, $data);
        $this->tester->seeRecord(\app\models\Account::class, [
            'profile_pic_url' => "/uploads/{$filename}",
        ]);

        $path = $service->profilePicPath($filename);
        unlink($path);
    }

    public function testUpdateMedia()
    {
        $service = new AccountDetails();

        $account = $this->tester->grabFixture('account', 'account1');
        $account = $this->tester->grabRecord(\app\models\Account::class, ['username' => $account->username]);

        $this->assertEmpty($account->media);

        $post1 = $this->tester->postDummyData();
        $post2 = $this->tester->postDummyData(2);

        $service->updateMedia($account, [$post1, $post2]);
        $account->refresh();

        $account = $this->tester->grabRecord(\app\models\Account::class, ['username' => $account->username]);
        $this->assertNotEmpty($account->media);
        $this->assertCount(2, $account->media);

        $post3 = $this->tester->postDummyData(3);
        $service->updateMedia($account, [$post1, $post2, $post3]);
        $account->refresh();
        $this->assertCount(3, $account->media);
    }
}
