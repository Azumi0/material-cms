<?php

use yii\db\Migration;

/**
 * Class m180501_163646_database_init
 */
class m180501_163646_database_init extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {

        $this->execute(file_get_contents(__DIR__ . '/init.sql'));
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        echo "m180501_163646_database_init cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180501_163646_database_init cannot be reverted.\n";

        return false;
    }
    */
}
