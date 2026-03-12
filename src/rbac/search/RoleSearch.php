<?php

namespace larikmc\admin\rbac\search;

use larikmc\admin\rbac\helpers\RbacHelper;
use Yii;
use yii\base\Model;
use yii\data\ArrayDataProvider;
use yii\rbac\Item;

class RoleSearch extends Model
{
    public ?string $name = null;

    public function rules(): array
    {
        return [
            ['name', 'trim'],
            ['name', 'string'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'description' => 'Описание',
            'ruleName' => 'Правило',
        ];
    }

    public function search(array $params): ArrayDataProvider
    {
        $this->load($params);
        $this->validate();

        $items = RbacHelper::sortItems(Yii::$app->authManager->getRoles());
        $rows = [];
        foreach ($items as $item) {
            if ($this->name && stripos($item->name, $this->name) === false) {
                continue;
            }

            $rows[] = [
                'name' => $item->name,
                'description' => $item->description,
                'ruleName' => $item->ruleName,
                'type' => Item::TYPE_ROLE,
            ];
        }

        return new ArrayDataProvider([
            'allModels' => $rows,
            'sort' => [
                'attributes' => ['name', 'description', 'ruleName'],
                'defaultOrder' => ['name' => SORT_ASC],
            ],
            'pagination' => ['pageSize' => 20],
        ]);
    }
}
