<?php

class Car extends AppModel {

	var $virtualFields = array(
		'km' => "CONCAT({Car}.id * 200, 'Km')",
	);

}