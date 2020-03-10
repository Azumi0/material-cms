<?php

use yii\db\Migration;

/**
 * Class m190217_153231_users_address
 */
class m190217_153231_users_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `g_users`
ADD `company_province` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `nip`,
ADD `company_city` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `company_province`;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190217_153231_users_address cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190217_153231_users_address cannot be reverted.\n";

        return false;
    }
    */
}
