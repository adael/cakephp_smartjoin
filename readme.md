# Playing with behavior for joins in cakephp

This is a test made for educational purposes and is not ready to use in production.

# Installation:

* Install cakephp 1.3.x
* edit app/config/core.php and set debug to 2
* Download smartjoin files: https://github.com/adael/cakephp_smartjoin/zipball/master
* Copy files into cakephp_installation/app/
* edit app/config/database.php and configure user, password and database (database can be an empty one)
* Execute tests.php (ie: http://localhost/cakeInstallFolder/tests.php)

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

### More advanced sample:

    $this->Person->find('first', array(
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
      left JOIN `cars` AS `Person__Mother__Car` ON (`Person__Mother`.`id` = `Person__Mother__Car`.`person_id`)
      left JOIN `offices` AS `Person__Mother__Office` ON (`Person__Mother`.`office_id` = `Person__Mother__Office`.`id`)
    WHERE `Person`.`id` = 1
    LIMIT 1