<?php

use yii\db\Migration;

/**
 * Class m190217_173231_example_users
 */
class m190217_173231_example_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('INSERT INTO `g_users` (`id`, `name`, `surname`, `mail`, `phone`, `password`, `salt`, `active`, `company_name`, `nip`, `company_province`, `company_city`, `company_address`, `base_address`, `extra_data`, `created`, `updated`, `role`, `type`, `agreement`) VALUES
(1,	\'Kamil\',	\'Odyn\',	\'odyn@somethingelse.com\',	\'668574023\',	\'$2y$13$tPjV9gJruJJ/0gyO/SsB2eOBdQxqQgh2lRjUWXbxSdZvENeK3PcDe\',	\'dnx6o\',	1,	\'\',	\'\',	\'\',	\'\',	\'\',	\'\',	\'{\"Obszar działania\": \"Cała polska\", \"Prace które mogę wykonywać\": \"Wycinka drzew/ek, Powierzchniowe usuwanie krzaków (mulczowanie)\", \"Doświadczenie w branży w latach\": \"3\"}\',	\'2018-01-12 10:21:30\',	\'2020-03-10 10:41:49\',	\'customer\',	\'frontend\',	0),
(7,	\'Mike\',	\'Doe\',	\'mikedoe@example.eu\',	\'789654202\',	\'$2y$13$GuugRyF3I18mWrJsef0TUOzHcHzTqo30RWW13WDA4/DX272hMxyee\',	\'n3AMf\',	1,	\'qweqwe\',	\'5246309924\',	\'Lubelskie\',	\'Sample\',	\'qweqwe 12/3\',	\'\',	NULL,	\'2018-01-12 10:23:04\',	\'2020-03-10 10:43:11\',	\'executor\',	\'frontend\',	0),
(8,	\'Joshn\',	\'Sample\',	\'joshn.sample@examplesecond.com\',	\'221458010\',	\'$2y$13$ggBMcPMHvgIY.io51Pbh2ehq0FmET0oka7Dbbp0oABNWvge6xZkAS\',	\'mKUI1\',	1,	\'\',	\'\',	\'\',	\'\',	\'\',	\'\',	NULL,	\'2019-02-01 11:02:38\',	\'2020-03-10 10:41:32\',	\'customer\',	\'frontend\',	1),
(9,	\'Karol\',	\'Example\',	\'fake@mymail.com\',	\'000000000\',	\'$2y$13$Z.hTgJZsNNWsAD4TtqtFg.QrX.XDXLCJZy/t2WBUe5J5Onz6274mG\',	\'Gppo8\',	1,	\'Sample company\',	\'5343661666\',	\'Dolnośląskie\',	\'Wrocław\',	\'Sample 12/2\',	\'derp 12\',	\'{\"machines\": [{\"id\": \"Traktor\", \"text\": \"Traktor\"}, {\"id\": \"kapeć\", \"text\": \"kapeć\"}, {\"id\": \"maszyna\", \"text\": \"maszyna\"}], \"area_type\": \"area_type_zone\", \"machine_park\": \"yes\", \"area_province\": {\"lodzkie\": false, \"slaskie\": false, \"lubuskie\": false, \"opolskie\": false, \"lubelskie\": false, \"podlaskie\": false, \"pomorskie\": false, \"malopolskie\": false, \"mazowieckie\": false, \"dolnoslaskie\": false, \"podkarpackie\": false, \"wielkopolskie\": false, \"swietokrzyskie\": false, \"kujawsko_pomorskie\": false, \"zachodniopomorskie\": false, \"warminsko_mazurskie\": false}, \"executor_skills\": {\"other_work\": \"salamandra, popo, tuta\", \"cutting_trees\": true, \"stabilization\": false, \"creating_belts\": false, \"max_frez_depth\": \"243\", \"mineralization\": false, \"removing_bushes\": true, \"removing_sticks\": false, \"removing_orchard\": false, \"removing_branches\": true, \"removing_selfsows\": true, \"removing_plantation\": false, \"max_trunk_diameter_frez\": \"12\", \"max_trunk_diameter_mulcz\": \"12\"}, \"type_zone_value\": \"3\", \"completed_orders\": \"3\", \"number_trees_cut\": \"12\", \"years_experience\": \"2\", \"finished_order_size\": \"12\", \"number_stabilization\": \"35\", \"number_bushes_removed\": \"5\", \"number_mineralization\": \"12\", \"number_branches_removed\": \"78\", \"number_small_bushes_removed\": \"65\"}\',	\'2019-02-02 11:42:06\',	\'2020-03-10 10:42:04\',	\'executor\',	\'frontend\',	1);');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190217_173231_example_users cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190217_173231_example_users cannot be reverted.\n";

        return false;
    }
    */
}
