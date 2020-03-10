<?php

use yii\db\Migration;

/**
 * Class m180624_194305_user_admin_update
 */
class m180624_194305_user_admin_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE `g_users`
ADD `agreement` tinyint(1) NOT NULL DEFAULT \'0\';');
        $this->execute('ALTER TABLE `g_admin`
CHANGE `access_token` `access_token` varchar(50) COLLATE \'utf8_polish_ci\' NULL AFTER `salt`;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180624_194305_user_admin_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180624_194305_user_admin_update cannot be reverted.\n";

        return false;
    }
    */
}
