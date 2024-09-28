<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%payments}}`.
 */
class m240928_092006_add_status_column_to_payments_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->addColumn('{{%payments}}', 'status', $this->string()->defaultValue('PENDING'));
    }

    public function down()
    {
        $this->dropColumn('{{%payments}}', 'status');
    }
}
