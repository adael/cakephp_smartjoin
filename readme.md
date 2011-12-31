# Playing with behavior for joins in cakephp

This is a test made for educational purposes and is not ready to use in production.

# Installation:

* Install cakephp 1.3.x
* edit app/config/core.php and set debug to 2
* Download smartjoin files: https://github.com/adael/cakephp_smartjoin/zipball/master
* Copy files into cakephp_installation/app/
* edit app/config/database.php and configure user, password and database (database can be an empty one)
* Execute tests.php (ie: http://localhost/cakeInstallFolder/tests.php)

# Features

(Explaining the features in english it's hard for me because is not my native language).

Well, I trying to do a system to get belongsTo and hasOne data from models with only one query and with a
"normalized resulset".

What I mean? (better I explain it with a code example)

    <?php
    # Somewhere inside a proyect...
    $this->Person->find('first', array(
        'contain' => array('Car'),
    ));
    ?>

The result I get is somethink like:

    array(
         'Person' => array(...person data...),
         'Car' => array(...car data...),
    );