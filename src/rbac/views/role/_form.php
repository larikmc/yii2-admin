<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>
<div class="rbac-role-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?= $form->field($model, 'children')->checkboxList($childOptions)->hint('Отметьте действия, которые должна получать эта роль.') ?>
    <?php if ($model->name === 'admin'): ?>
        <div class="alert alert-warning">Системная роль `admin` защищена от удаления. Действие `adminPanel` всегда остается привязанным к ней.</div>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
