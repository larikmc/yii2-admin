<?php

namespace larikmc\admin\rbac\forms;

use yii\base\Model;

class UserAssignmentForm extends Model
{
    public string|int|null $userId = null;
    private array $_items = [];

    public function rules(): array
    {
        return [
            [['userId'], 'required'],
            [['items'], 'each', 'rule' => ['string']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'items' => 'Роли',
        ];
    }

    public function getItems(): array
    {
        return $this->_items;
    }

    public function setItems($items): void
    {
        if ($items === null || $items === '') {
            $this->_items = [];
            return;
        }

        $this->_items = is_array($items) ? array_values($items) : [(string) $items];
    }
}
