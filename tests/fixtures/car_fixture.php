<?php

class CarFixture extends CakeTestFixture {

	var $name = 'Car';
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'model' => array('type' => 'string', 'length' => 50),
		'created' => 'datetime',
		'updated' => 'datetime'
	);
	var $records = array(
		array('id' => 1, 'model' => 'Renault Clio'),
		array('id' => 2, 'model' => 'BMW Z3'),
		array('id' => 3, 'model' => 'Ford K'),
		array('id' => 4, 'model' => 'Seat Ibiza'),
		array('id' => 5, 'model' => 'Renault Megane'),
		array('id' => 6, 'model' => 'Smart'),
	);

}
