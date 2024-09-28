
<?php

use yii\bootstrap5\ActiveForm;

?>
<div class="site-index">
    <div class="hero min-h-screen bg-base-200">
        <div class="hero-content flex-col lg:flex-row-reverse">
            <div class="text-center lg:text-left">
                <img src="web/pay.svg" alt="illustration" width="400">
            </div>
            <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
                <?php ActiveForm::begin([
                    'method'=> 'post',
                    'action'=> '/payments/create',
                ]) ?>
                <!-- <form class="card-body" method="POST" action="stk.php"> -->
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Phone Number</span>
                        </label>
                        <input type="text" placeholder="254xxxxxxx" class="input input-bordered" name="phonenumber" required />
                    </div>
                    <div class="form-control">
                        <label class="label">
                            <span class="label-text">Amount</span>
                        </label>
                        <input type="text" placeholder="10" class="input input-bordered" name="amount" required />
                    </div>
                    <div class="form-control mt-6">
                        <button class="btn btn-primary" type="submit" name="deposit">Make Payment</button>
                    </div>
                <!-- </form> -->
                <?php ActiveForm::end() ?>

            </div>
        </div>
    </div>
</div>