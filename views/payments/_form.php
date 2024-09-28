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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Start polling every few seconds
        let invoiceId = '<?= Yii::$app->session->get('invoice_id'); ?>'; // Get invoice ID from session
        if (invoiceId) {
            let checkInterval = setInterval(function() {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['site/check-status']); ?>',
                    type: 'GET',
                    data: {
                        invoice_id: invoiceId
                    },
                    success: function(response) {
                        let result = JSON.parse(response);

                        if (result.status !== 'PROCESSING') {
                            clearInterval(checkInterval); // Stop polling if transaction is complete
                            alert('Transaction Status: ' + result.status);
                            // Optionally redirect the user or show the final status in the UI
                        }
                    },
                    error: function() {
                        alert('Error checking transaction status.');
                        clearInterval(checkInterval); // Stop polling on error
                    }
                });
            }, 5000); // Poll every 5 seconds
        }
    });
</script>