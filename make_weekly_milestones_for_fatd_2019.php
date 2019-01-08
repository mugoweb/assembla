<?php

require_once './Assembla.php';

use MugoWeb\Assembla;

// create a personal API key and secret at https://app.assembla.com/user/edit/manage_clients
$assembla = new Assembla( "", "" );


$weeks = array();

$year           = 2019;
$firstDayOfYear = mktime(0, 0, 0, 1, 1, $year);
$nextMonday     = strtotime('monday', $firstDayOfYear);
$nextFriday     = strtotime('friday', $nextMonday);

while (date('Y', $nextMonday) == $year) {
    $weeks[] = array(
            date("m", $nextMonday) . "-" . date("d", $nextMonday),
            date("m", $nextFriday) . "-" . date("d", $nextFriday)
    );
    $nextMonday = strtotime('+1 week', $nextMonday);
    $nextFriday = strtotime('+1 week', $nextFriday);
}

$index = 1;

foreach ($weeks as $week) {
    if ($index == sizeof($weeks) ) {
        print_r($assembla->createMilestone("dOdF-OZ6Or5ANcdmr6CpXy", '{ "milestone": { "title": "[19.'. $index .'] 2019-'.$week[0].'", "start_date": "2019-'.$week[0].'T00:00:00Z", "due_date": "2020-'.$week[1].'T00:00:00Z", "user_id": "dSh1g0EqOr5OoLdmr6CpXy" } }'));
    } else {
        print_r($assembla->createMilestone("dOdF-OZ6Or5ANcdmr6CpXy", '{ "milestone": { "title": "[19.'. $index .'] 2019-'.$week[0].'", "start_date": "2019-'.$week[0].'T00:00:00Z", "due_date": "2019-'.$week[1].'T00:00:00Z", "user_id": "dSh1g0EqOr5OoLdmr6CpXy" } }'));
    }
    $index++;
}
