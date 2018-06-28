<?php


use Codeception\Util\HttpCode;
use Helper\Api;

class AccountCest
{
    public function _before(ApiTester $I)
    {
        $I->haveFixtures([
            'account' => \app\tests\fixtures\AccountFixture::class,
            'user' => \app\tests\fixtures\UserFixture::class,
        ]);

        $user = $I->grabFixture('user', 'user1');
        $I->amBearerAuthenticated($user->access_token);
    }

    public function tryToGetList(ApiTester $I)
    {
        $I->sendGET('/accounts');
        $I->seeProperListResponse(Api::responseAccountJsonType());
    }

    public function tryToGetFilteredList(ApiTester $I)
    {
        /** @var \app\modules\api\v1\models\Account $account5 */
        $account5 = $I->grabFixture('account', 'account5');
        /** @var \app\modules\api\v1\models\Account $account7 */
        $account7 = $I->grabFixture('account', 'account7');

        $I->sendGET('/accounts', [
            'filter' => [
                'username' => $account5->username,
            ],
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $response = $I->grabJsonResponseAsArray();
        $I->assertCount(1, $response);
        $I->assertEquals($account5->username, $response['0']['username']);
        $I->assertEquals($account5->name, $response['0']['name']);

        $I->sendGET('/accounts', [
            'filter' => [
                'username' => ['in' => [$account5->username, $account7->username]],
            ],
            'sort' => 'id',
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $response = $I->grabJsonResponseAsArray();
        $I->assertCount(2, $response);
        $I->assertEquals($account5->username, $response['0']['username']);
        $I->assertEquals($account5->name, $response['0']['name']);
        $I->assertEquals($account7->username, $response['1']['username']);
        $I->assertEquals($account7->name, $response['1']['name']);

        $I->sendGET('/accounts', [
            'filter' => [
                'username' => ['in' => [$account5->username, $account7->username]],
            ],
            'sort' => 'id',
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $response = $I->grabJsonResponseAsArray();
        $I->assertCount(2, $response);
        $I->assertEquals($account5->username, $response['0']['username']);
        $I->assertEquals($account5->name, $response['0']['name']);
        $I->assertEquals($account7->username, $response['1']['username']);
        $I->assertEquals($account7->name, $response['1']['name']);

    }

    public function tryToGetFilteredListByStats(ApiTester $I)
    {
        $I->haveFixtures([
            'account_stats' => \app\tests\fixtures\AccountStatsFixture::class,
        ]);

        $I->sendGET('/accounts', [
            'filter' => [
                'er' => ['>' => 0.5],
                'or' => [
                    ['follows' => 10],
                    ['followed_by' => 9],
                ],
            ],
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $response = $I->grabJsonResponseAsArray();
        $I->assertCount(2, $response);
    }

    public function tryToGetFilteredListByTags(ApiTester $I)
    {
        $I->haveFixtures([
            'tag' => \app\tests\fixtures\TagFixture::class,
            'account_tag' => \app\tests\fixtures\AccountTagFixture::class,
        ]);

        $I->sendGET('/accounts', [
            'filter' => [
                'tags' => 'tag1',
            ],
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $I->assertCount(1, $I->grabJsonResponseAsArray());

        $account = $I->grabFixture('account', 'account1');
        $tag = $I->grabFixture('tag', 'tag2');
        $user = $I->grabFixture('user', 'user1');
//
        $I->haveRecord(\app\models\AccountTag::class, ['account_id' => $account->id, 'tag_id' => $tag->id, 'user_id' => $user->id]);
//
        $I->sendGET('/accounts', [
            'filter' => [
                'tags' => "tag1, {$tag->name}",
            ],
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $I->assertCount(1, $I->grabJsonResponseAsArray());

        $account2 = $I->grabFixture('account', 'account2');
        $I->haveRecord(\app\models\AccountTag::class, ['account_id' => $account2->id, 'tag_id' => $tag->id, 'user_id' => $user->id]);

        $I->sendGET('/accounts', [
            'filter' => [
                'tags' => $tag->name,
            ],
        ]);

        $I->seeProperListResponse(Api::responseAccountJsonType());
        $I->assertCount(2, $I->grabJsonResponseAsArray());
    }

    public function tryCreateAccount(ApiTester $I)
    {
        $I->sendPOST('/accounts', [
            'username' => 'test1',
            'monitoring' => 1,
//            'name' => "TEST-1",
        ]);
        $I->seeProperResourceResponse(Api::responseAccountJsonType(), HttpCode::CREATED);

        $I->sendPOST('/accounts', [
            'username' => 'test1',
            'monitoring' => 1,
            'name' => "TEST-1",
            'tags' => 'test1, test2',
        ]);
        $I->seeProperResourceResponse(Api::responseAccountJsonType(), HttpCode::OK);
    }
}
