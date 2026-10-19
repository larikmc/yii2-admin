<?php

namespace larikmc\admin\tests\unit;

use PHPUnit\Framework\TestCase;
use larikmc\admin\services\TotpService;
use larikmc\admin\services\TwoFactorService;
use larikmc\admin\rbac\helpers\SystemRbacHelper;
use Yii;
use yii\web\Application;

class TwoFactorTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        new Application([
            'id' => 'test-app',
            'basePath' => __DIR__ . '/../../',
            'components' => [
                'db' => [
                    'class' => \yii\db\Connection::class,
                    'dsn' => 'sqlite::memory:',
                ],
                'cache' => [
                    'class' => \yii\caching\ArrayCache::class,
                ],
                'security' => [
                    'class' => \yii\base\Security::class,
                ],
                'request' => [
                    'class' => \yii\web\Request::class,
                    'cookieValidationKey' => 'test_key_32_characters_long_abcdef',
                    'scriptFile' => __DIR__ . '/index.php',
                    'scriptUrl' => '/index.php',
                ],
                'user' => [
                    'class' => \yii\web\User::class,
                    'identityClass' => \larikmc\admin\tests\unit\MockUser::class,
                    'enableAutoLogin' => true,
                ],
                'authManager' => [
                    'class' => \yii\rbac\DbManager::class,
                ],
            ],
            'params' => [
                'admin.2fa.secretKey' => 'test_random_key_of_at_least_32_characters_long_12345',
            ],
            'modules' => [
                'admin' => [
                    'class' => \larikmc\admin\Module::class,
                    'userClass' => \larikmc\admin\tests\unit\MockUser::class,
                    'rootUserId' => '42',
                ],
            ],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        Yii::$app = null;
        parent::tearDownAfterClass();
    }

    public function testSystemRbacHelperRootUserIdIsConfigurable(): void
    {
        // Admin module configured rootUserId is '42'
        $this->assertEquals('42', SystemRbacHelper::getRootUserId());
        $this->assertTrue(SystemRbacHelper::isRootUser('42'));
        $this->assertTrue(SystemRbacHelper::isRootUser(42));
        $this->assertFalse(SystemRbacHelper::isRootUser('1')); // arbitrary user 1 is NOT root!
        $this->assertFalse(SystemRbacHelper::isRootUser(null));
    }

    public function testTotpSecretGenerationAndVerification(): void
    {
        $totp = new TotpService();
        $secret = $totp->generateSecret();

        $this->assertEquals(32, strlen($secret));
        $this->assertMatchesRegularExpression('/^[A-Z2-7]+$/', $secret);

        $now = 1700000000;
        $slice = (int) floor($now / 30);
        $code = $totp->calculateCode($secret, $slice);

        $this->assertEquals(6, strlen($code));
        $this->assertTrue(ctype_digit($code));

        // Valid verification
        $this->assertTrue($totp->verifyCode($secret, $code, 1, $now));

        // Valid in previous and next window (+- 30s)
        $this->assertTrue($totp->verifyCode($secret, $code, 1, $now + 25));
        $this->assertTrue($totp->verifyCode($secret, $code, 1, $now - 25));

        // Invalid code rejection
        $this->assertFalse($totp->verifyCode($secret, '000000', 1, $now));
        $this->assertFalse($totp->verifyCode($secret, '999999', 1, $now));
        $this->assertFalse($totp->verifyCode($secret, 'abc', 1, $now));
    }

    public function testTwoFactorServiceLifecycle(): void
    {
        $service = new TwoFactorService(['secretKey' => 'unit-test-secret-key-32-characters-min-abc']);
        $service->ensureTablesInstalled();

        $userId = '100';

        // Initially disabled
        $this->assertFalse($service->isTwoFactorEnabled($userId));

        // Generate setup secret
        $setup = $service->generateSecretForSetup($userId);
        $this->assertNotEmpty($setup['secret']);
        $secret = $setup['secret'];

        // Confirm and enable with valid code
        $now = time();
        $slice = (int) floor($now / 30);
        $code = $service->getTotp()->calculateCode($secret, $slice);

        $confirmed = $service->confirmAndEnable($userId, $secret, $code);
        $this->assertTrue($confirmed);
        $this->assertTrue($service->isTwoFactorEnabled($userId));

        // Generate backup codes
        $backupCodes = $service->generateBackupCodes($userId, 8);
        $this->assertCount(8, $backupCodes);
        $this->assertEquals(8, $service->getRemainingBackupCodesCount($userId));

        // Verify with TOTP code
        $verifyTotp = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertTrue($verifyTotp);

        // Anti-replay: same code cannot be reused immediately!
        $reused = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertFalse($reused, 'Replay attack with same TOTP code must be rejected.');

        // Verify with backup code
        $oneBackupCode = $backupCodes[0];
        $verifyBackup = $service->verifyTotpOrBackupCode($userId, $oneBackupCode);
        $this->assertTrue($verifyBackup, 'Single-use backup code must be accepted.');

        // Backup code consumed count decrements
        $this->assertEquals(7, $service->getRemainingBackupCodesCount($userId));

        // Reusing same backup code fails
        $reuseBackup = $service->verifyTotpOrBackupCode($userId, $oneBackupCode);
        $this->assertFalse($reuseBackup, 'Used backup code cannot be used again.');

        // Disable 2FA
        $service->disable($userId);
        $this->assertFalse($service->isTwoFactorEnabled($userId));
        $this->assertEquals(0, $service->getRemainingBackupCodesCount($userId));
    }

    public function testSecretEncryptionDecryption(): void
    {
        $service = new TwoFactorService(['secretKey' => 'my-super-secret-encryption-key-32b']);
        $rawSecret = 'JBSWY3DPEHPK3PXP';

        $encrypted = $service->encryptSecret($rawSecret);
        $this->assertNotEquals($rawSecret, $encrypted);

        $decrypted = $service->decryptSecret($encrypted);
        $this->assertEquals($rawSecret, $decrypted);
    }

    public function testPasswordResetRevokesAuthKey(): void
    {
        $user = new MockUser();
        $user->auth_key = 'old_session_key';

        $oldKey = $user->getAuthKey();
        $user->generateAuthKey();
        $this->assertNotEquals($oldKey, $user->getAuthKey());
        $this->assertFalse($user->validateAuthKey('old_session_key'));
    }

    public function testLoginFormValidationAndUserResolution(): void
    {
        $form = new \larikmc\admin\auth\models\LoginForm([
            'userClass' => MockUser::class,
            'email' => 'admin@example.com',
            'password' => 'CorrectPassword123!',
        ]);

        $this->assertTrue($form->validate());
        $this->assertNotNull($form->getUser());
        $this->assertEquals('100', $form->getUser()->getId());

        $invalidForm = new \larikmc\admin\auth\models\LoginForm([
            'userClass' => MockUser::class,
            'email' => 'admin@example.com',
            'password' => 'WrongPassword',
        ]);
        $this->assertFalse($invalidForm->validate());
    }

    public function testTwoFactorEmergencyConsoleReset(): void
    {
        $service = new TwoFactorService();
        $userId = '100';

        // Setup and enable 2FA
        $setup = $service->generateSecretForSetup($userId);
        $slice = (int) floor(time() / 30);
        $code = $service->getTotp()->calculateCode($setup['secret'], $slice);
        $service->confirmAndEnable($userId, $setup['secret'], $code);
        $service->generateBackupCodes($userId);

        $this->assertTrue($service->isTwoFactorEnabled($userId));
        $this->assertGreaterThan(0, $service->getRemainingBackupCodesCount($userId));

        // Invoke Console command
        $controller = new \larikmc\admin\commands\TwoFactorController('admin-2fa', Yii::$app);
        $exitCode = $controller->actionReset('admin@example.com');

        $this->assertEquals(\yii\console\ExitCode::OK, $exitCode);
        $this->assertFalse($service->isTwoFactorEnabled($userId));
        $this->assertEquals(0, $service->getRemainingBackupCodesCount($userId));
    }

    public function testNewInstallWithoutConfiguredRootDoesNotAutoGrantAdmin(): void
    {
        // When rootUserId is null (unconfigured default)
        $adminModule = Yii::$app->getModule('admin');
        $oldRoot = $adminModule->rootUserId;

        try {
            $adminModule->rootUserId = null;
            $this->assertNull(SystemRbacHelper::getRootUserId());
            $this->assertFalse(SystemRbacHelper::isRootUser('1'));
            $this->assertFalse(SystemRbacHelper::isRootUser('42'));
        } finally {
            $adminModule->rootUserId = $oldRoot;
        }
    }

    public function testTwoFactorSessionGuardLogic(): void
    {
        $service = new TwoFactorService();
        $userId = '100';

        // Enable 2FA for user
        $setup = $service->generateSecretForSetup($userId);
        $slice = (int) floor(time() / 30);
        $code = $service->getTotp()->calculateCode($setup['secret'], $slice);
        $service->confirmAndEnable($userId, $setup['secret'], $code);

        $session = Yii::$app->session;

        // Case 1: Session does NOT have auth_2fa_passed_user_id (e.g. fresh auto-login via cookie)
        $session->remove('auth_2fa_passed_user_id');
        $session->remove('auth_2fa_token');
        $passed = $session->get('auth_2fa_passed_user_id');
        $this->assertNull($passed);

        // Case 2: Session has verified 2FA passed with valid authKey token
        $user = MockUser::findIdentity($userId);
        $token = hash('sha256', (string) $user->getAuthKey());
        $session->set('auth_2fa_passed_user_id', (string) $userId);
        $session->set('auth_2fa_token', $token);

        $this->assertEquals($userId, $session->get('auth_2fa_passed_user_id'));
        $this->assertEquals($token, $session->get('auth_2fa_token'));

        // Case 3: Identity regenerates authKey (centralized revocation) -> token mismatch!
        $user->generateAuthKey();
        $newToken = hash('sha256', (string) $user->getAuthKey());
        $this->assertNotEquals($newToken, $session->get('auth_2fa_token'));
    }

    public function testSecretKeyRequirementThrowsWhenMissingOrShort(): void
    {
        $backupParams = Yii::$app->params['admin.2fa.secretKey'] ?? null;
        unset(Yii::$app->params['admin.2fa.secretKey']);

        try {
            $serviceNoKey = new TwoFactorService(['secretKey' => '']);
            $this->assertFalse($serviceNoKey->isConfigured());

            $this->expectException(\yii\base\InvalidConfigException::class);
            $serviceNoKey->resolveSecretKey();
        } finally {
            if ($backupParams !== null) {
                Yii::$app->params['admin.2fa.secretKey'] = $backupParams;
            }
        }
    }

    public function testSecretKeyRequirementThrowsWhenTooShort(): void
    {
        $serviceShort = new TwoFactorService(['secretKey' => 'short-key-less-than-32-chars']);
        $this->assertFalse($serviceShort->isConfigured());

        $this->expectException(\yii\base\InvalidConfigException::class);
        $serviceShort->resolveSecretKey();
    }

    public function testTotpReplayProtectionBlocksDuplicateUse(): void
    {
        $service = new TwoFactorService();
        $userId = '100';

        // Setup and enable 2FA
        $setup = $service->generateSecretForSetup($userId);
        $slice = (int) floor(time() / 30);
        $code = $service->getTotp()->calculateCode($setup['secret'], $slice);
        $service->confirmAndEnable($userId, $setup['secret'], $code);

        // First verification: should succeed
        $firstVerify = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertTrue($firstVerify);

        // Immediate second verification with SAME TOTP code: MUST be rejected by anti-replay!
        $secondVerify = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertFalse($secondVerify, 'Повторный ввод того же TOTP-кода должен блокироваться анти-replay защитой!');
    }

    public function testBackupCodeAtomicSingleUse(): void
    {
        $service = new TwoFactorService();
        $userId = '100';

        // Generate backup codes
        $backupCodes = $service->generateBackupCodes($userId);
        $this->assertCount(8, $backupCodes);
        $code = $backupCodes[0];

        // First use: must succeed
        $firstResult = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertTrue($firstResult);
        $this->assertEquals(7, $service->getRemainingBackupCodesCount($userId));

        // Second use (attempted reuse / double spending): MUST fail
        $secondResult = $service->verifyTotpOrBackupCode($userId, $code);
        $this->assertFalse($secondResult, 'Резервный код не может быть использован дважды!');
        $this->assertEquals(7, $service->getRemainingBackupCodesCount($userId));
    }
}

class MockUser extends \yii\base\Model implements \yii\web\IdentityInterface
{
    public $id = '100';
    public $username = 'admin';
    public $email = 'admin@example.com';
    public $auth_key = 'test_auth_key';
    public $password_hash = '';

    public static function findIdentity($id)
    {
        if ($id == '100' || $id == '42') {
            $user = new self();
            $user->id = (string) $id;
            return $user;
        }
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public static function findByEmail($email)
    {
        if ($email === 'admin@example.com') {
            $user = new self();
            $user->id = '100';
            return $user;
        }
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    public function validatePassword($password)
    {
        return $password === 'CorrectPassword123!';
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        return true;
    }
}
