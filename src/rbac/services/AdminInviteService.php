<?php

namespace larikmc\admin\rbac\services;

use Yii;

class AdminInviteService
{
    private const CACHE_PREFIX = 'admin_invite_token_';
    private const SESSION_KEY = 'admin.inviteToken';

    public function createInvite(int $ttl = 86400): array
    {
        $token = Yii::$app->security->generateRandomString(48);
        $expiresAt = time() + max(300, $ttl);

        Yii::$app->cache->set(
            $this->cacheKey($token),
            [
                'createdAt' => time(),
                'expiresAt' => $expiresAt,
                'used' => false,
            ],
            max(300, $ttl)
        );

        return [
            'token' => $token,
            'expiresAt' => $expiresAt,
        ];
    }

    public function validateToken(string $token): bool
    {
        if ($token === '') {
            return false;
        }

        $data = Yii::$app->cache->get($this->cacheKey($token));
        if (!is_array($data)) {
            return false;
        }

        if (($data['used'] ?? false) === true) {
            return false;
        }

        return (int)($data['expiresAt'] ?? 0) > time();
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

        Yii::$app->cache->delete($this->cacheKey($token));

        return true;
    }

    private function cacheKey(string $token): string
    {
        return self::CACHE_PREFIX . hash('sha256', $token);
    }
}
