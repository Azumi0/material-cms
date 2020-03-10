<?php

use yii\db\Migration;

/**
 * Class m180627_210658_gus_integration
 */
class m180627_210658_gus_integration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->execute('ALTER TABLE `g_users`
CHANGE `nip` `company_name` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `active`,
ADD `nip` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `company_name`,
CHANGE `base_address` `company_address` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `nip`,
ADD `base_address` varchar(255) COLLATE \'utf8_polish_ci\' NULL AFTER `company_address`;');
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m180627_210658_gus_integration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180627_210658_gus_integration cannot be reverted.\n";

        return false;
    }
    */
}
