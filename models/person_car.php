<?php

class PersonCar extends AppModel {

	var $belongsTo = array(
		'Person' => array(
			'className' => 'SmartJoin.Person',
		),
		'Car' => array(
			'className' => 'SmartJoin.car',
		),
	);

}