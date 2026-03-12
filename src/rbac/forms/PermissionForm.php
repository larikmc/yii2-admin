<?php

namespace larikmc\admin\rbac\forms;

use yii\base\Model;
use yii\rbac\Item;

class PermissionForm extends Model
{
    public string $name = '';
    public ?string $description = null;
    public ?string $ruleName = null;
    public int $type = Item::TYPE_PERMISSION;
    private array $_children = [];

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['description', 'ruleName'], 'string'],
            [['children'], 'each', 'rule' => ['string']],
            ['name', 'match', 'pattern' => '/^[a-zA-Z0-9_\-\.\/]+$/'],
            ['type', 'in', 'range' => [Item::TYPE_PERMISSION]],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'description' => 'Описание',
            'ruleName' => 'Правило',
            'children' => 'Дочерние действия',
        ];
    }

    public function getChildren(): array
    {
        return $this->_children;
    }

    public function setChildren($children): void
    {
        if ($children === null || $children === '') {
            $this->_children = [];
            return;
        }

        $this->_children = is_array($children) ? array_values($children) : [(string) $children];
    }
}
