<?php

namespace larikmc\admin\rbac\services;

use Yii;

class AdminInviteService
{
    private const TABLE_NAME = '{{%admin_invite}}';
    private const SESSION_KEY = 'admin.inviteToken';

    public function createInvite(int $ttl = 86400): array
    {
        $this->ensureStorage();
        $token = Yii::$app->security->generateRandomString(48);
        $expiresAt = time() + max(300, $ttl);
        $tokenHash = $this->tokenHash($token);

        Yii::$app->db->createCommand()->insert(self::TABLE_NAME, [
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'created_at' => time(),
            'used_at' => null,
            'used_by' => null,
        ])->execute();

        return [
            'token' => $token,
            'expiresAt' => $expiresAt,
        ];
    }

    public function validateToken(string $token): bool
    {
        $this->ensureStorage();
        if ($token === '') {
            return false;
        }

        $invite = $this->findInviteByToken($token);
        if (!is_array($invite)) {
            return false;
        }

        if (!empty($invite['used_at'])) {
            return false;
        }

        return (int)($invite['expires_at'] ?? 0) > time();
    }

    public function rememberToken(string $token): void
    {
        Yii::$app->session->set(self::SESSION_KEY, $token);
    }

    public function takeRememberedToken(): ?string
    {
        $token = (string)Yii::$app->session->get(self::SESSION_KEY, '');
        Yii::$app->session->remove(self::SESSION_KEY);

        return $token !== '' ? $token : null;
    }

    public function consumeToken(string $token, string|int $userId): bool
    {
        $this->ensureStorage();
        if (!$this->validateToken($token)) {
            return false;
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole('admin');
        if ($role === null) {
            return false;
        }

        if ($auth->getAssignment('admin', $userId) === null) {
            $auth->assign($role, $userId);
        }

        Yii::$app->db->createCommand()->update(
            self::TABLE_NAME,
            [
                'used_at' => time(),
                'used_by' => (string) $userId,
            ],
            ['token_hash' => $this->tokenHash($token)]
        )->execute();

        return true;
    }

    private function findInviteByToken(string $token): array|false
    {
        return Yii::$app->db->createCommand(
            'SELECT token_hash, expires_at, created_at, used_at, used_by FROM ' . self::TABLE_NAME . ' WHERE token_hash = :tokenHash LIMIT 1',
            [':tokenHash' => $this->tokenHash($token)]
        )->queryOne();
    }

    private function tokenHash(string $token): string
    {
        return hash('sha256', $token);
    }

    private function ensureStorage(): void
    {
        $db = Yii::$app->db;
        if ($db->schema->getTableSchema(self::TABLE_NAME, true) !== null) {
            return;
        }

        $db->createCommand()->createTable(self::TABLE_NAME, [
            'id' => 'pk',
            'token_hash' => 'string(64) NOT NULL',
            'expires_at' => 'integer NOT NULL',
            'created_at' => 'integer NOT NULL',
            'used_at' => 'integer NULL',
            'used_by' => 'string(64) NULL',
        ])->execute();
        $db->createCommand()->createIndex('idx_admin_invite_token_hash', self::TABLE_NAME, 'token_hash', true)->execute();
        $db->createCommand()->createIndex('idx_admin_invite_expires_at', self::TABLE_NAME, 'expires_at')->execute();
        $db->createCommand()->createIndex('idx_admin_invite_used_at', self::TABLE_NAME, 'used_at')->execute();
    }
}
