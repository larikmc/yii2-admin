<?php

namespace larikmc\admin\auth\models;

use yii\base\Model;

class TwoFactorForm extends Model
{
    public $code;

    public function rules(): array
    {
        return [
            ['code', 'required', 'message' => 'Введите проверочный код.'],
            ['code', 'trim'],
            ['code', 'string', 'min' => 6, 'max' => 12],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'code' => 'Код подтверждения (TOTP или резервный код)',
        ];
    }
}
