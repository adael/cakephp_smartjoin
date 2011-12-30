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
	var $fixtures = array('person', 'office', 'car');

	/**
	 * Method executed before each test
	 *
	 * @access public
	 */
	function startTest() {
		$this->Person = & ClassRegistry::init('Person');
		$this->Person->recursive = -1;
		$this->Person->Behaviors->attach('SmartJoin');
	}

	/**
	 * Method executed after each test
	 *
	 * @access public
	 */
	function endTest() {
		unset($this->Person);
		ClassRegistry::flush();
	}

	function testHasOne() {
		$r = $this->Person->find('first', array(
			'assoc' => array(
				'Car'
			),
			'conditions' => array('Person.id' => 1),
				));

		$this->assertTrue(isset($r['Person']['name']));
		$this->assertTrue(isset($r['Person']['Car']));
		$this->assertFalse(isset($r['Person']['Car']['model']));

		$r = $this->Person->find('first', array(
			'assoc' => array(
				'Car'
			),
			'conditions' => array('Person.id' => 4),
				));

		$this->assertTrue(isset($r['Person']['name']));
		$this->assertTrue(isset($r['Person']['Car']['model']));
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
					'Office',
					'Car' => array(
						'fields' => array(
							'model',
							'km',
							'({Car}.id * 24) as {Car}__test',
						),
					),
				),
			),
			'conditions' => array('Person.id' => 1),
				));
		$this->assertTrue($r['Person']['Mother']);
		$this->assertEqual($r['Person']['Mother']['name'], 'Elsa');
		$this->assertEqual($r['Person']['Mother']['Car']['test'], 72);
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
				'Car' => array(
					'fields' => array(
						'model',
						'km',
						'({Car}.id * 24) as {Car}__test',
					),
				),
			),
			'conditions' => array('Person.id' => 4),
				));

		$this->assertTrue(isset($r['Person']['name']));
		$this->assertFalse(isset($r['Person']['id']));
		$this->assertTrue(isset($r['Person']['Car']));
		$this->assertFalse(isset($r['Car']));
		$this->assertFalse(isset($r['Person']['Mother']));

		$this->assertEqual($r['Person']['name'], 'Tony');
		$this->assertEqual($r['Person']['Car']['model'], 'BMW Z3');
	}

}
