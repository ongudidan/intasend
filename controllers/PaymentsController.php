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
        $transaction = Yii::$app->db->beginTransaction(); // Start a transaction

        try {
            if ($this->request->isPost) {
                if ($model->load($this->request->post()) && $model->save()) {
                    $phone = $model->phone;
                    $amount = $model->amount;

                    // Initiate the STK Push
                    $status = $this->initiateStkPush($amount, $phone);

                    // Check if status is a success or error
                    if (strpos($status, 'Error') !== false) {
                        Yii::$app->session->setFlash('error', 'Payment failed: ' . $status);
                        $model->status = 'FAILED';
                    } else {
                        if ($status === 'COMPLETE') {
                            $model->status = 'PAID';
                            Yii::$app->session->setFlash('success', 'Payment successful. Status: ' . $status);
                        } else {
                            $model->status = 'PENDING';
                            Yii::$app->session->setFlash('warning', 'Payment is processing. Status: ' . $status);
                        }
                    }

                    if (!$model->save()) {
                        throw new \Exception('Failed to update payment status.');
                    }

                    $transaction->commit(); // Commit transaction if everything went well

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $model->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            $transaction->rollBack(); // Rollback in case of error
            Yii::$app->session->setFlash('error', 'Error processing payment: ' . $e->getMessage());
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    // public function initiateStkPush($amount, $phone_number)
    // {
    //     // Define your credentials
    //     $credentials = [
    //         'token' => 'ISSecretKey_test_691abd3d-84d5-4c9b-a4e1-801a4aa7e404',
    //         'publishable_key' => 'ISPubKey_test_c1825e70-974c-4fdb-861f-cec6ae1d1d2d',
    //         'test' => true,
    //     ];

    //     // Initialize the Collection class
    //     $collection = new Collection();
    //     $collection->init($credentials);

    //     // Initiate the STK push
    //     $response = $collection->mpesa_stk_push($amount, $phone_number);

    //     // Check if the response is valid
    //     if (isset($response->invoice) && isset($response->invoice->invoice_id)) {
    //         $invoice_id = $response->invoice->invoice_id;
    //     } else {
    //         // Handle error if the invoice ID is not present in the response
    //         return 'Error: Invalid response from STK push initiation.';
    //     }

    //     // Initialize status variable
    //     $status = "PROCESSING";

    //     // Check the status of the invoice
    //     while ($status == "PROCESSING") {
    //         sleep(1); // Delay for a second before checking status again

    //         // Check the status of the invoice
    //         $statusResponse = $collection->status($invoice_id);

    //         // Check if the status response is valid
    //         if (isset($statusResponse->invoice) && isset($statusResponse->invoice->state)) {
    //             $status = $statusResponse->invoice->state;
    //         } else {
    //             // Handle error if the status response is not valid
    //             return 'Error: Unable to retrieve status for the invoice.';
    //         }
    //     }

    //     return $status;
    // }

    public function initiateStkPush($amount, $phone_number)
    {

        global $credentials;

        $collection = new Collection();
        $collection->init($credentials);

        //initiating the stk push
        $response = $collection->mpesa_stk_push($amount, $phone_number);

        //Get Invoive ID
        $invoice_id = $response->invoice->invoice_id;

        //initialize status variable
        $status = "PROCESSING";

        //check the status
        while ($status == "PROCESSING") {

            sleep(1);

            //check
            $statusResponse = $collection->status($invoice_id);
            //get update on status

            $status = $statusResponse->invoice->state;
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
