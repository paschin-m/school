<?php

use yii\db\Migration;

/**
 * Handles adding authokey to table `{{%user}}`.
 */
class m190417_100020_add_authokey_column_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'authokey', $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'authokey');
    }
}
