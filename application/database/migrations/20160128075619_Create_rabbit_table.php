<?php

/**
 * Migration: Create Rabbit Table
 *
 * Created by: SprintPHP
 * Created on: 2016-01-28 07:56am
 *
 * @property $dbforge
 */
class Migration_create_rabbit_table extends CI_Migration {

    public function up ()
    {
        $fields = [
		'name' => [
			'type' => 'varchar',
			'constraint' => 255,
		],		'id' => [
			'type' => 'int',
			'constraint' => 9,
			'auto_increment' => true,
			'unsigned' => true,
		],		'created_on' => [
			'type' => 'datetime',
		],		'modified_on' => [
			'type' => 'datetime',
		],		'age' => [
			'type' => 'int',
			'constraint' => 2,
		],	];

        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
	    $this->dbforge->create_table('rabbits', true, config_item('migration_create_table_attr') );
    
    }

    //--------------------------------------------------------------------

    public function down ()
    {
        $this->dbforge->drop_table('rabbits');
    }

    //--------------------------------------------------------------------

}