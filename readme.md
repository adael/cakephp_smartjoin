# Playing with behavior for joins in cakephp

This is a test made for educational purposes and is not ready to use in production. I had to create a datasource with
some code modification for the desired results. I ran the tests with these modifications
(models and behaviors) without problems.

# Installation and review:

* Install cakephp 1.3.x
* edit app/config/database.php and set 'driver' => 'SmartJoin.MysqlEx'
* Download smartjoin plugin and install: https://github.com/adael/cakephp_smartjoin/zipball/master
* Execute tests.php (ie: http://localhost/cakeInstallFolder/tests.php)
* Choose SmartJoin tests

# Features and Ideas
* Use {ModelName} in virtualFields or customFields, the behavior automatically replaces it with the correct table alias. (see advanced example).
* The behavior uses virtualFields of associated models (but I think need more tests for ensure this feature)
* The behavior collects join configuration from association config (defined in $belongsTo and $hasMany), but you
can easily redefine in the call.
* You don't need to include fields that you don't need. In containable you must to include foreign keys of related models (afaik)
* The response allways puts the associated model inside the results of parent model. ie: Car inside Person, etc

# Usage

    # A person hasOne => Car
    # Somewhere inside a project...
    $this->Person->find('first', array(
        'assoc' => array(
            'Car'
        ),
    ));

    // Outputs:
    array(
        'Person' => array(
            // person fields
            'Car' => array(
                // Car fields
            ),
        ),
    )

    // SQL:
    SELECT
      `Person`.`id`,
      `Person`.`name`,
      `Person`.`office_id`,
      `Person`.`mother_id`,
      `Person`.`created`,
      `Person`.`updated`,
      `Person__Car`.`id`,
      `Person__Car`.`model`,
      `Person__Car`.`person_id`,
      `Person__Car`.`created`,
      `Person__Car`.`updated`,
      (CONCAT(Person__Car.id * 200, 'Km')) AS `Person__Car__km`
    FROM
      `people` AS `Person`
      left JOIN `cars` AS `Person__Car` ON (`Person`.`id` = `Person__Car`.`person_id`)
    WHERE `Person`.`id` = 1
    LIMIT 1

### An (not much yet) advanced example:

This example use models defined in this repository (check models/)

    $this->Person->find('first', array(
        'assoc' => array(
            'Mother' => array(
                'Office',
                'Car' => array(
					'type' => 'inner', // <-- note that I can change easily the join type
                    'fields' => array(
                        'model',
                        'km',
                        '({Car}.id * 24) as {Car}__test', // <-- note that {Car} is replaced with actual alias
                    ),
                ),
            ),
        ),
        'conditions' => array('Person.id' => 1),
    ));

    // Output:
    Array
    (
        [Person] => Array (
                [id] => 1
                [name] => Bob
                [office_id] => 1
                [mother_id] => 8
                [created] =>
                [updated] =>
                [Mother] => Array (
                        [id] => 8
                        [name] => Elsa
                        [office_id] => 1
                        [mother_id] => 0
                        [created] =>
                        [updated] =>
                        [Car] => Array (
                                [model] => Ford K
                                [test] => 72
                                [km] => 600Km
                            )
                        [Office] => Array (
                                [id] => 1
                                [name] => Office 1
                                [address] => Office 1 address
                                [created] =>
                                [updated] =>
                            )
                    )
            )
    )

    // SQL
    SELECT
      `Person`.`id`,
      `Person`.`name`,
      `Person`.`office_id`,
      `Person`.`mother_id`,
      `Person`.`created`,
      `Person`.`updated`,
      `Person__Mother`.`id`,
      `Person__Mother`.`name`,
      `Person__Mother`.`office_id`,
      `Person__Mother`.`mother_id`,
      `Person__Mother`.`created`,
      `Person__Mother`.`updated`,
      `Person__Mother__Car`.`model`,
      (Person__Mother__Car.id * 24) as Person__Mother__Car__test,
      (CONCAT(Person__Mother__Car.id * 200, 'Km')) AS `Person__Mother__Car__km`,
      `Person__Mother__Office`.`id`,
      `Person__Mother__Office`.`name`,
      `Person__Mother__Office`.`address`,
      `Person__Mother__Office`.`created`,
      `Person__Mother__Office`.`updated`
    FROM `people` AS `Person`
      left JOIN `people` AS `Person__Mother` ON (`Person`.`mother_id` = `Person__Mother`.`id`)
      inner JOIN `cars` AS `Person__Mother__Car` ON (`Person__Mother`.`id` = `Person__Mother__Car`.`person_id`)
      left JOIN `offices` AS `Person__Mother__Office` ON (`Person__Mother`.`office_id` = `Person__Mother__Office`.`id`)
    WHERE `Person`.`id` = 1
    LIMIT 1