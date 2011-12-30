<?php

class CarFixture extends CakeTestFixture {

	var $name = 'car';
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'model' => array('type' => 'string', 'length' => 50),
		'person_id' => array('type' => 'integer', 'null' => false),
		'created' => 'datetime',
		'updated' => 'datetime'
	);
	var $records = array(
		array('model' => 'Renault Clio', 'person_id' => 2),
		array('model' => 'BMW Z3', 'person_id' => 4),
		array('model' => 'Ford K', 'person_id' => 8),
	);

}
