<?php

use Codeception\Util\HttpCode;


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;

    public function seeProperResourceResponse($jsonType, $httpCode = HttpCode::OK, $strict = true, $jsonPath = null)
    {
        $this->seeResponseCodeIs($httpCode);
        $this->seeResponseIsJson();
        $this->seeResponseIsNotEmpty();
        $this->seeResponseMatchesJsonType($jsonType, $jsonPath);
        if ($jsonPath) {
            $jsonArray = new \Codeception\Util\JsonArray($this->grabResponse());
            $response = $jsonArray->filterByJsonPath($jsonPath);
            $this->seeJsonResponseHasKeys(array_keys($jsonType), $strict, false, $response[0]);
        } else {
            $this->seeJsonResponseHasKeys(array_keys($jsonType), $strict);

        }
    }

    public function seeProperListResponse($jsonType)
    {
        $this->seeResponseCodeIs(HttpCode::OK);
        $this->seeResponseIsJson();
        $this->seeResponseIsNotEmpty();
        $this->seeResponseMatchesJsonType($jsonType);
        $this->seeJsonResponseHasKeys(array_keys($jsonType), true, true);
    }

    public function seeResponseIsNotEmpty()
    {
        $this->assertNotEmpty($this->grabJsonResponseAsArray());
    }


    public function seeJsonResponseHasKeys($keys, $strict = true, $multiple = false, $response = [])
    {
        sort($keys);
        $response = $response ?: $this->grabJsonResponseAsArray();
        if ($multiple === false) {
            $response = [$response];
        }
        foreach ($response as $item) {
            $itemKeys = array_keys($item);
            sort($itemKeys);

            if ($strict) {
                $this->assertEquals($keys, $itemKeys);
            } else {
                foreach ($keys as $k) {
                    $this->assertArrayHasKey($k, $item);
                }
            }

        }
    }

    public function grabJsonResponseAsArray()
    {
        return json_decode($this->grabResponse(), true);
    }
}
