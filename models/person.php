<?php

class Person extends AppModel {

	var $hasOne = array(
		'Car' => array(
			'className' => 'SmartJoin.Car',
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