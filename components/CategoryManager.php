<?php
/**
 * Created for IG Monitoring.
 * User: jakim <pawel@jakimowski.info>
 * Date: 2019-01-31
 */

namespace app\components;


use app\components\traits\BatchInsertCommandTrait;
use app\models\Account;
use app\models\AccountCategory;
use app\models\Category;
use app\models\Tag;
use app\models\User;
use yii\base\BaseObject;

class CategoryManager extends BaseObject
{
    use BatchInsertCommandTrait;

    public function getForUser(User $user, bool $asInt = false)
    {
        $q = Category::find()
            ->distinct()
            ->andWhere(['or',
                ['category.id' => $user->getAccountCategories()->select('account_category.category_id')],
//                ['category.id' => $user->getTagCategories()->select('tag_category.category_id')],
            ]);

        return $asInt ? $q->select('category.id')->column() : $q->all();
    }

    /**
     * @param \app\models\User $user
     * @param Account|Account[]|null $accounts
     * @param bool $asInt
     * @return Category[]|int[]
     */
    public function getForUserAccounts(User $user, $accounts = null, bool $asInt = false)
    {
        $accounts = $accounts instanceof Account ? [$accounts->id] : ArrayHelper::getColumn($accounts, 'id');

        $q = Category::find()
            ->innerJoin('account_category ac', 'category.id=ac.category_id AND ac.user_id=' . (int)$user->id)
            ->andFilterWhere(['ac.account_id' => $accounts]);


        return $asInt ? $q->select('category.id')->column() : $q->all();
    }

    /**
     * @param \app\models\User $user
     * @param Tag|Tag[]|null $tags
     * @param bool $asInt
     * @return Category[]|int[]
     */
    public function getForUserTags(User $user, $tags = null, bool $asInt = false)
    {
        $tags = $tags instanceof Tag ? [$tags->id] : ArrayHelper::getColumn($tags, 'id');

        $q = Category::find()
            ->innerJoin('tag_category ac', 'category.id=ac.category_id AND ac.user_id=' . (int)$user->id)
            ->andFilterWhere(['ac.tag_id' => $tags]);


        return $asInt ? $q->select('category.id')->column() : $q->all();
    }

    /**
     * It deletes the previous ones and sets new ones.
     *
     * @param \app\models\Account $account
     * @param array $categories
     * @param \app\models\User $user
     * @throws \yii\db\Exception
     */
    public function saveForAccount(Account $account, array $categories, User $user)
    {
        AccountCategory::deleteAll([
            'AND',
            [
                'account_id' => $account->id,
                'user_id' => $user->id,
            ],
            ['NOT', ['category_id' => Category::find()->andWhere(['name' => $categories])->column()]],
        ]);
        $this->addToAccount($account, $categories, $user);
    }

    /**
     * Adds new ones.
     *
     * @param \app\models\Account $account
     * @param array $categories
     * @param \app\models\User $user
     * @throws \yii\db\Exception
     */
    public function addToAccount(Account $account, array $categories, User $user)
    {
        $this->saveCategories($categories);
        $rows = array_map(function ($categoryId) use ($account, $user) {
            return [
                $account->id,
                $categoryId,
                $user->id,
            ];
        }, Category::find()->andWhere(['name' => $categories])->column());

        $this->batchInsertIgnoreCommand(AccountCategory::tableName(), ['account_id', 'category_id', 'user_id'], $rows)
            ->execute();
    }

//    /**
//     * It deletes the previous ones and sets new ones.
//     *
//     * @param \app\models\Tag $tag
//     * @param array $categories
//     * @param \app\models\User $user
//     * @throws \yii\db\Exception
//     */
//    public function saveForTag(Tag $tag, array $categories, User $user)
//    {
//        TagCategory::deleteAll([
//            'AND',
//            [
//                'tag_id' => $tag->id,
//                'user_id' => $user->id,
//            ],
//            ['NOT', ['category_id' => Category::find()->andWhere(['name' => $categories])->column()]],
//        ]);
//        $this->addToTag($tag, $categories, $user);
//    }

//    /**
//     * Adds new ones.
//     *
//     * @param \app\models\Tag $tag
//     * @param array $categories
//     * @param \app\models\User $user
//     * @throws \yii\db\Exception
//     */
//    public function addToTag(Tag $tag, array $categories, User $user)
//    {
//        $this->saveCategories($categories);
//        $rows = array_map(function ($categoryId) use ($tag, $user) {
//            return [
//                $tag->id,
//                $categoryId,
//                $user->id,
//            ];
//        }, Category::find()->andWhere(['name' => $categories])->column());
//
//        $this->batchInsertIgnoreCommand(TagCategory::tableName(), ['tag_id', 'category_id', 'user_id'], $rows)
//            ->execute();
//    }


    public function saveCategories(array $categories)
    {
        $rows = array_map(function ($category) {
            return [$category];
        }, $categories);

        $this->batchInsertIgnoreCommand(Category::tableName(), ['name'], $rows)
            ->execute();
    }
}