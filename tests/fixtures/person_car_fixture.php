<?php

class PersonCarFixture extends CakeTestFixture {

	var $name = 'PersonCar';
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'person_id' => array('type' => 'integer', 'null' => false),
		'car_id' => array('type' => 'integer', 'null' => false),
		'main' => array('type' => 'integer', 'null' => false, 'length' => 1),
	);
	var $records = array(
		array('person_id' => 1, 'car_id' => 4, 'main' => 1),
		array('person_id' => 1, 'car_id' => 5),
		array('person_id' => 2, 'car_id' => 6, 'main' => 1),
		array('person_id' => 3, 'car_id' => 3),
		array('person_id' => 4, 'car_id' => 6, 'main' => 1),
		array('person_id' => 4, 'car_id' => 2),
		array('person_id' => 8, 'car_id' => 3, 'main' => 1),
	);

}
