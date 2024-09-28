<?php

namespace app\controllers;

use app\models\Payments;
use app\models\PaymentsSearch;
use IntaSend\IntaSendPHP\Checkout;
use IntaSend\IntaSendPHP\Collection;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Payments models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PaymentsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Payments model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Payments model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Payments();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $phone = $model->phone;
                $amount = $model->amount;

                // Initiate STK push and get the invoice ID
                $invoice_id = $this->initiateStkPush($amount, $phone);

                // If the invoice ID was successfully retrieved
                if ($invoice_id) {
                    // Initialize a status variable
                    $status = "PROCESSING";

                    // Loop until the payment status is no longer "PROCESSING"
                    while ($status === "PROCESSING" || $status = null) {
                        sleep(1); // Optional: Avoid too many requests in a short time

                        // Check the status using the invoice ID
                        $statusResponse = $this->checkPaymentStatus($invoice_id);
                        $status = $statusResponse->invoice->state; // Assuming this is how you get the status

                        // Optional: You can log or debug the status if needed
                        Yii::debug("Current payment status: $status");
                    }

                    // Set the model attributes based on the final status
                    $model->invoice_id = $invoice_id; // Store the invoice ID
                    $model->status = $status; // Set the final status

                    // Save the model to the database
                    if ($model->save()) {
                        Yii::$app->session->setFlash('success', 'Payment completed successfully. Status: ' . $status);
                        return $this->redirect(['view', 'id' => $model->id]);
                    } else {
                        Yii::$app->session->setFlash('error', 'Failed to save payment record.');
                    }
                } else {
                    Yii::$app->session->setFlash('error', 'Failed to initiate payment.');
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    // Method to check payment status
    protected function checkPaymentStatus($invoice_id)
    {
        // Define your credentials
        $credentials = [
            'token' => 'ISSecretKey_test_691abd3d-84d5-4c9b-a4e1-801a4aa7e404',
            'publishable_key' => 'ISPubKey_test_c1825e70-974c-4fdb-861f-cec6ae1d1d2d',
            'test' => true,
        ];

        $collection = new Collection();
        $collection->init($credentials);

        // Check the status using the invoice ID
        return $collection->status($invoice_id);
    }

    public function initiateStkPush($amount, $phone_number)
    {
        // Define your credentials
        $credentials = [
            'token' => 'ISSecretKey_test_691abd3d-84d5-4c9b-a4e1-801a4aa7e404',
            'publishable_key' => 'ISPubKey_test_c1825e70-974c-4fdb-861f-cec6ae1d1d2d',
            'test' => true,
        ];

        $collection = new Collection();
        $collection->init($credentials);

        // Initiating the STK push
        $response = $collection->mpesa_stk_push($amount, $phone_number);

        // Get Invoice ID
        $invoice_id = $response->invoice->invoice_id;

        // Initialize status variable
        $status = "PROCESSING";

        // Check the status
        while ($status == "PROCESSING") {
            sleep(1);

            // Check
            $statusResponse = $collection->status($invoice_id);
            // Get update on status
            $status = $statusResponse->invoice->state;
        }

        // Set flash message based on status
        if ($status === 'COMPLETE') {
            Yii::$app->session->setFlash('paymentStatus', [
                'class' => 'alert-success',
                'message' => 'Payment Successful'
            ]);
        } elseif ($status === 'FAILED') {
            Yii::$app->session->setFlash('paymentStatus', [
                'class' => 'alert-error',
                'message' => 'Payment Cancelled'
            ]);
        } else {
            Yii::$app->session->setFlash('paymentStatus', [
                'class' => 'alert-warning',
                'message' => 'Payment Pending'
            ]);
        }

        return $status;
    }



    /**
     * Updates an existing Payments model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Payments model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payments::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
