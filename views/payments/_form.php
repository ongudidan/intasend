<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Payments $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="payments-form">
<!-- 
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput() ?>

    <?= $form->field($model, 'amount')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div> -->

    <?php ActiveForm::end(); ?>

    <?php ActiveForm::begin() ?>
    <!-- <form class="card-body" method="POST" action="stk.php"> -->
    <div class="form-control">
        <label class="label">
            <span class="label-text">Phone Number</span>
        </label>
        <?= $form->field($model, 'phone')->textInput() ?>
    </div>
    <div class="form-control">
        <label class="label">
            <span class="label-text">Amount</span>
        </label>
        <?= $form->field($model, 'amount')->textInput() ?>
    </div>
    <div class="form-control mt-6">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <!-- </form> -->
    <?php ActiveForm::end() ?>

</div>