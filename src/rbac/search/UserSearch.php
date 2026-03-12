<?php

namespace larikmc\admin\rbac\search;

use larikmc\admin\rbac\Module;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    public ?string $global = null;

    public function rules(): array
    {
        return [
            ['global', 'trim'],
            ['global', 'string'],
        ];
    }

    public function search(array $params, Module $module): ActiveDataProvider
    {
        $this->load($params);
        $this->validate();

        $class = $module->getConfig()->userModel;
        $query = $class::find();
        $config = $module->getConfig();

        if ($this->global) {
            $query->andFilterWhere(['or',
                ['like', $config->usernameField, $this->global],
                ['like', $config->emailField, $this->global],
                [$config->userIdField => $this->global],
            ]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => [$config->userIdField => SORT_DESC]],
            'pagination' => ['pageSize' => 20],
        ]);
    }
}
