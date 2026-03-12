<?php

namespace larikmc\admin\rbac\migrations;

use Yii;
use yii\db\Migration;
use yii\rbac\DbManager;

class m000001_create_rbac_tables extends Migration
{
    public function safeUp()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;
        $tableOptions = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;

        if ($this->db->schema->getTableSchema($auth->ruleTable, true) === null) {
            $this->createTable($auth->ruleTable, [
                'name' => $this->string(64)->notNull(),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ], $tableOptions);
        }

        if ($this->db->schema->getTableSchema($auth->itemTable, true) === null) {
            $this->createTable($auth->itemTable, [
                'name' => $this->string(64)->notNull(),
                'type' => $this->smallInteger()->notNull(),
                'description' => $this->text(),
                'rule_name' => $this->string(64),
                'data' => $this->binary(),
                'created_at' => $this->integer(),
                'updated_at' => $this->integer(),
                'PRIMARY KEY ([[name]])',
            ], $tableOptions);
            $this->addForeignKey('fk-auth_item-rule_name', $auth->itemTable, 'rule_name', $auth->ruleTable, 'name', 'SET NULL', 'CASCADE');
            $this->createIndex('idx-auth_item-type', $auth->itemTable, 'type');
        }

        if ($this->db->schema->getTableSchema($auth->itemChildTable, true) === null) {
            $this->createTable($auth->itemChildTable, [
                'parent' => $this->string(64)->notNull(),
                'child' => $this->string(64)->notNull(),
                'PRIMARY KEY ([[parent]], [[child]])',
            ], $tableOptions);
            $this->addForeignKey('fk-auth_item_child-parent', $auth->itemChildTable, 'parent', $auth->itemTable, 'name', 'CASCADE', 'CASCADE');
            $this->addForeignKey('fk-auth_item_child-child', $auth->itemChildTable, 'child', $auth->itemTable, 'name', 'CASCADE', 'CASCADE');
        }

        if ($this->db->schema->getTableSchema($auth->assignmentTable, true) === null) {
            $this->createTable($auth->assignmentTable, [
                'item_name' => $this->string(64)->notNull(),
                'user_id' => $this->string(64)->notNull(),
                'created_at' => $this->integer(),
                'PRIMARY KEY ([[item_name]], [[user_id]])',
            ], $tableOptions);
            $this->addForeignKey('fk-auth_assignment-item_name', $auth->assignmentTable, 'item_name', $auth->itemTable, 'name', 'CASCADE', 'CASCADE');
            $this->createIndex('idx-auth_assignment-user_id', $auth->assignmentTable, 'user_id');
        }
    }

    public function safeDown()
    {
        /** @var DbManager $auth */
        $auth = Yii::$app->authManager;

        if ($this->db->schema->getTableSchema($auth->assignmentTable, true) !== null) {
            $this->dropTable($auth->assignmentTable);
        }
        if ($this->db->schema->getTableSchema($auth->itemChildTable, true) !== null) {
            $this->dropTable($auth->itemChildTable);
        }
        if ($this->db->schema->getTableSchema($auth->itemTable, true) !== null) {
            $this->dropTable($auth->itemTable);
        }
        if ($this->db->schema->getTableSchema($auth->ruleTable, true) !== null) {
            $this->dropTable($auth->ruleTable);
        }
    }
}
