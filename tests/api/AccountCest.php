<?php


use Codeception\Util\HttpCode;

class AccountCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'account' => \app\tests\fixtures\AccountFixture::class,
            'user' => \app\tests\fixtures\UserFixture::class,
        ]);
    }

    // tests
    public function tryCreateAccount(ApiTester $I)
    {
        $user = $I->grabFixture('user', 'user1');
        $I->amBearerAuthenticated($user->access_token);

        $I->sendPOST('/accounts', [
            'username' => 'test1',
            'monitoring' => 1,
//            'name' => "TEST-1",
        ]);
        $I->seeProperResourceResponse(\Helper\Api::responseAccountJsonType(), HttpCode::CREATED);

        $I->sendPOST('/accounts', [
            'username' => 'test1',
            'monitoring' => 1,
            'name' => "TEST-1",
        ]);
        $I->seeProperResourceResponse(\Helper\Api::responseAccountJsonType(), HttpCode::OK);
    }
}
