<?php

class OfficeFixture extends CakeTestFixture {

	var $name = 'Office';
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 50),
		'address' => array('type' => 'string', 'length' => 50),
		'created' => 'datetime',
		'updated' => 'datetime'
	);
	var $records = array(
		array('id' => 1, 'name' => 'Office 1', 'address' => 'Office 1 address'),
		array('id' => 2, 'name' => 'Office 2', 'address' => 'Office 2 address'),
		array('id' => 3, 'name' => 'Office 3', 'address' => 'Office 3 address'),
	);

}
