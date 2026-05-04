<?php

namespace larikmc\admin\rbac\search;

use larikmc\admin\rbac\Module;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends Model
{
    public ?string $global = null;
    public ?string $username = null;
    public ?string $email = null;
    public ?string $role = null;
    public ?string $hasAssignments = null;
    public int $roleCount = 0;

    public function rules(): array
    {
        return [
            ['global', 'trim'],
            ['global', 'string'],
            [['username', 'email', 'role', 'hasAssignments'], 'trim'],
            [['username', 'email', 'role', 'hasAssignments'], 'string'],
            ['hasAssignments', 'in', 'range' => ['1', '0']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'username' => 'Логин',
            'email' => 'Email',
            'role' => 'Роль',
            'hasAssignments' => 'Назначения',
        ];
    }

    public function search(array $params, Module $module, bool $assignedOnly = false): ActiveDataProvider
    {
        $this->load($params);
        $this->validate();

        $class = $module->getConfig()->userModel;
        $query = $class::find();
        $config = $module->getConfig();
        $auth = Yii::$app->authManager;
        $assignmentTable = $auth->assignmentTable;
        $itemTable = $auth->itemTable;

        $query->alias('u');
        $query->select(['u.*', 'roleCount' => 'COUNT(DISTINCT ai.name)']);
        $query->leftJoin(['aa' => $assignmentTable], 'aa.user_id = [[u.' . $config->userIdField . ']]');
        $query->leftJoin(['ai' => $itemTable], 'ai.name = aa.item_name AND ai.type = 1');
        $query->groupBy(['u.' . $config->userIdField]);
        $query->distinct();

        if ($this->global) {
            $query->andFilterWhere(['or',
                ['like', 'u.' . $config->usernameField, $this->global],
                ['like', 'u.' . $config->emailField, $this->global],
                ['u.' . $config->userIdField => $this->global],
            ]);
        }

        $query->andFilterWhere(['like', 'u.' . $config->usernameField, $this->username]);
        $query->andFilterWhere(['like', 'u.' . $config->emailField, $this->email]);

        if ($this->role) {
            $query->andWhere(['aa.item_name' => $this->role]);
        }

        if ($this->hasAssignments === '1') {
            $query->andWhere(['not', ['aa.item_name' => null]]);
        } elseif ($this->hasAssignments === '0') {
            $query->andWhere(['aa.item_name' => null]);
        }

        if ($assignedOnly) {
            $query->andWhere(['not', ['aa.item_name' => null]]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [$config->userIdField => SORT_DESC],
                'attributes' => [
                    $config->userIdField => [
                        'asc' => ['u.' . $config->userIdField => SORT_ASC],
                        'desc' => ['u.' . $config->userIdField => SORT_DESC],
                    ],
                    $config->usernameField => [
                        'asc' => ['u.' . $config->usernameField => SORT_ASC],
                        'desc' => ['u.' . $config->usernameField => SORT_DESC],
                    ],
                    $config->emailField => [
                        'asc' => ['u.' . $config->emailField => SORT_ASC],
                        'desc' => ['u.' . $config->emailField => SORT_DESC],
                    ],
                    $config->statusField => [
                        'asc' => ['u.' . $config->statusField => SORT_ASC],
                        'desc' => ['u.' . $config->statusField => SORT_DESC],
                    ],
                    'roleCount' => [
                        'asc' => ['roleCount' => SORT_ASC],
                        'desc' => ['roleCount' => SORT_DESC],
                    ],
                ],
            ],
            'pagination' => ['pageSize' => 20],
        ]);
    }

    public function getRoleOptions(): array
    {
        $roles = Yii::$app->authManager->getRoles();
        $options = [];

        foreach ($roles as $role) {
            $options[$role->name] = $role->name;
        }

        asort($options);

        return $options;
    }

    public function getAssignmentStateOptions(): array
    {
        return [
            '1' => 'С ролями',
            '0' => 'Без ролей',
        ];
    }
}
