<?php

/**
 * SmartJoinBehaviorTest file
 */
App::import('Core', array('AppModel', 'Model'));

/**
 * SmartJoinTest class
 */
class SmartJoinBehaviorTest extends CakeTestCase {

	/**
	 * Fixtures associated with this test case
	 *
	 * @var array
	 * @access public
	 */
	var $fixtures = array(
		'plugin.smart_join.car',
		'plugin.smart_join.office',
		'plugin.smart_join.person',
		'plugin.smart_join.person_car',
	);

	/**
	 * Method executed before each test
	 *
	 * @access public
	 */
	function start() {
		parent::start();
		$this->Person = ClassRegistry::init('SmartJoin.Person');
		$this->Person->recursive = -1;
		$this->Person->Behaviors->attach('SmartJoin.SmartJoin');
	}

	function testHasOne() {
		$r = $this->Person->find('first', array(
			'fields' => array(
				'Person.name',
			),
			'assoc' => array(
				'MainPersonCar' => array(
					'fields' => false,
					'Car' => array(
						'fields' => 'model',
					),
				),
			),
			'conditions' => array('Person.id' => 1),
				));

		$this->assertTrue(isset($r['Person']['name']));
		$this->assertTrue(isset($r['Person']['MainPersonCar']['Car']));
		$this->assertEqual($r['Person']['MainPersonCar']['Car']['model'], 'Seat Ibiza');

		$r = $this->Person->find('first', array(
			'assoc' => array(
				'MainPersonCar' => array(
					'Car' => array(
					),
				),
			),
			'conditions' => array('Person.id' => 4),
				));

		$this->assertEqual($r['Person']['name'], 'Tony');
		$this->assertEqual($r['Person']['MainPersonCar']['Car']['model'], 'Smart');
	}

	function testBelongsTo() {
		$r = $this->Person->find('first', array(
			'assoc' => array(
				'Mother',
			),
			'conditions' => array('Person.id' => 1),
				));
		$this->assertTrue($r['Person']['Mother']);
		$this->assertEqual($r['Person']['Mother']['name'], 'Elsa');
	}

	function testDeepBelongsTo() {
		$r = $this->Person->find('first', array(
			'assoc' => array(
				'Mother' => array(
					'Office' => array(
					),
					'MainPersonCar' => array(
						'type' => 'inner',
						'Car' => array(
							'fields' => array(
								'model',
								'km',
								'({Car}.id * 24) as {Car}__test',
							),
						),
					),
				),
			),
			'conditions' => array('Person.id' => 1),
				));

		$this->assertFalse(isset($r['Person']['MainPersoncar']));
		$this->assertEqual($r['Person']['name'], 'Bob');
		$this->assertEqual($r['Person']['Mother']['name'], 'Elsa');
		$this->assertEqual($r['Person']['Mother']['Office']['name'], 'Office 1');
		$this->assertEqual($r['Person']['Mother']['MainPersonCar']['Car']['test'], 72);
		$this->assertEqual($r['Person']['Mother']['MainPersonCar']['Car']['model'], 'Ford K');
	}

	/**
	 * testWithVirtualFieldsAndCustomFieldsOnRelations
	 *
	 * @access public
	 * @return void
	 */
	function testWithVirtualFieldsAndCustomFieldsOnRelations() {
		$r = $this->Person->find('first', array(
			'fields' => array('name'),
			'assoc' => array(
				'MainPersonCar' => array(
					'Car' => array(
						'fields' => array(
							'model',
							'km',
							'({Car}.id * 24) as {Car}__test',
						),
					),
				),
			),
			'conditions' => array('Person.id' => 4),
				));

		$this->assertFalse(isset($r['Person']['id']));
		$this->assertFalse(isset($r['Person']['Mother']));
		$this->assertEqual($r['Person']['name'], 'Tony');
		$this->assertEqual($r['Person']['MainPersonCar']['Car']['model'], 'Smart');
	}

}
