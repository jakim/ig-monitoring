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
        $data = $this->accountNewData();

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
        $data = $this->accountNewData();

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
        $data = $this->accountNewData();
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

        $post1 = new \Jakim\Model\Post();
        $post1->id = 'test1';
        $post1->shortcode = 'test1';
        $post1->url = 'test1';
        $post1->isVideo = false;
        $post1->caption = 'test1';
        $post1->likes = 1;
        $post1->comments = 1;
        $post1->takenAt = time();

        $post2 = new \Jakim\Model\Post();
        $post2->id = 'test2';
        $post2->shortcode = 'test2';
        $post2->url = 'test2';
        $post2->isVideo = false;
        $post2->caption = 'test2';
        $post2->likes = 2;
        $post2->comments = 2;
        $post2->takenAt = time();

        $service->updateMedia($account, [$post1, $post2]);

        $account = $this->tester->grabRecord(\app\models\Account::class, ['username' => $account->username]);
        $this->assertNotEmpty($account->media);
        $this->assertCount(2, $account->media);
    }

    /**
     * @return \Jakim\Model\Account
     */
    protected function accountNewData(): \Jakim\Model\Account
    {
        $data = new \Jakim\Model\Account();
        $data->username = 'test_data1';
        $data->fullName = 'test_data1';
        $data->biography = 'test_data1';
        $data->externalUrl = 'test_data1';
        $data->id = 'test_data1';
        $data->profilePicUrl = 'test_profile_pic.jpg';

        return $data;
    }
}
