<?php

class Person extends AppModel {

	var $hasOne = array(
		'MainPersonCar' => array(
			'className' => 'SmartJoin.PersonCar',
			'foreignKey' => 'person_id',
			'conditions' => array(
				'{MainPersonCar}.main' => 1,
			),
		),
	);
	var $hasMany = array(
		'PersonCar' => array(
			'className' => 'SmartJoin.PersonCar',
		),
	);
	var $belongsTo = array(
		'Mother' => array(
			'className' => 'SmartJoin.Person',
			'foreignKey' => 'mother_id',
		),
		'Office' => array(
			'className' => 'SmartJoin.Office',
		),
	);

}