<?php

class PersonFixture extends CakeTestFixture {

	var $name = 'Person';
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 50),
		'office_id' => array('type' => 'integer', 'null' => false),
		'mother_id' => array('type' => 'integer', 'null' => false),
		'best_friend_id' => array('type' => 'integer', 'null' => false),
		'created' => 'datetime',
		'updated' => 'datetime'
	);
	var $records = array(
		array('id' => 1, 'name' => 'Bob', 'office_id' => 1, 'mother_id' => 8),
		array('id' => 2, 'name' => 'John', 'mother_id' => 8),
		array('id' => 3, 'name' => 'Fred', 'office_id' => 1, 'mother_id' => 8, 'best_friend_id' => 4),
		array('id' => 4, 'name' => 'Tony', 'mother_id' => 10),
		array('id' => 5, 'name' => 'Charles', 'office_id' => 2),
		array('id' => 6, 'name' => 'Bill', 'office_id' => 2),
		array('id' => 7, 'name' => 'Miles', 'office_id' => 2),
		array('id' => 8, 'name' => 'Elsa', 'office_id' => 1),
		array('id' => 9, 'name' => 'Maria'),
		array('id' => 10, 'name' => 'Eva'),
	);

}
