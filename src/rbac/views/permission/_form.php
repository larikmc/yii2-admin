<?php

use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
?>
<div class="rbac-permission-form">
    <?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>
    <?php if ($model->name === 'adminPanel'): ?>
        <div class="alert alert-warning">Системное действие `adminPanel` защищено от удаления и используется для доступа в админ-панель.</div>
    <?php endif; ?>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Отмена', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
