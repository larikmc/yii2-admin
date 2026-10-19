<?php

namespace larikmc\admin\migrations;

use yii\db\Migration;

class m000002_create_admin_2fa_tables extends Migration
{
    private const TABLE_2FA = '{{%admin_user_2fa}}';
    private const TABLE_BACKUP = '{{%admin_user_2fa_backup_code}}';

    public function safeUp()
    {
        $tableOptions = $this->db->driverName === 'mysql'
            ? 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB'
            : null;

        if ($this->db->schema->getTableSchema(self::TABLE_2FA, true) === null) {
            $this->createTable(self::TABLE_2FA, [
                'id' => $this->primaryKey(),
                'user_id' => $this->string(64)->notNull(),
                'secret' => $this->text()->notNull(),
                'is_enabled' => $this->boolean()->notNull()->defaultValue(false),
                'created_at' => $this->integer()->notNull(),
                'enabled_at' => $this->integer()->null(),
                'last_used_at' => $this->integer()->null(),
            ], $tableOptions);

            $this->createIndex('idx_admin_user_2fa_user_id', self::TABLE_2FA, 'user_id', true);
            $this->createIndex('idx_admin_user_2fa_is_enabled', self::TABLE_2FA, 'is_enabled');
        }

        if ($this->db->schema->getTableSchema(self::TABLE_BACKUP, true) === null) {
            $this->createTable(self::TABLE_BACKUP, [
                'id' => $this->primaryKey(),
                'user_id' => $this->string(64)->notNull(),
                'code_hash' => $this->string(255)->notNull(),
                'used_at' => $this->integer()->null(),
                'created_at' => $this->integer()->notNull(),
            ], $tableOptions);

            $this->createIndex('idx_admin_user_2fa_backup_user_id', self::TABLE_BACKUP, 'user_id');
            $this->createIndex('idx_admin_user_2fa_backup_used_at', self::TABLE_BACKUP, 'used_at');
        }
    }

    public function safeDown()
    {
        if ($this->db->schema->getTableSchema(self::TABLE_BACKUP, true) !== null) {
            $this->dropTable(self::TABLE_BACKUP);
        }

        if ($this->db->schema->getTableSchema(self::TABLE_2FA, true) !== null) {
            $this->dropTable(self::TABLE_2FA);
        }
    }
}
