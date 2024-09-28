<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%payments}}`.
 */
class m240928_113734_create_payments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%payments}}', [
            'id' => $this->primaryKey(),
            'amount' => $this->decimal(10, 2)->notNull(), // Payment amount
            'phone' => $this->string(15)->notNull(), // User's phone number
            'invoice_id' => $this->string(), // M-Pesa invoice ID
            'status' => $this->string()->defaultValue('PENDING'), // Payment status
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'), // Creation timestamp
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'), // Update timestamp
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%payments}}');
    }
}
