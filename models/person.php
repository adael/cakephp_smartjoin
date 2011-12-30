<?php

class Person extends AppModel {

	var $hasOne = array(
		'Car' => array(
		),
	);
	var $belongsTo = array(
		'Mother' => array(
			'className' => 'Person',
			'foreignKey' => 'mother_id',
		),
		'Office' => array(
		),
	);

}