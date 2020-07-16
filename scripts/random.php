<?php
$csv = file_get_contents("random.txt");

$lines = explode("\n", $csv);
$a = array();
foreach ($lines as $line) {
    $a[] = explode(",", $line);
}

foreach ($a as $student) {
    $user = \App\User::where(array(array('student_id', '=', $student[0])))->first();

    if (!is_null($user)) {
        $aval_club = \App\Club::fetchWarClubs();
        $randIndex = array_rand($aval_club);
        $user->club_id = $aval_club[$randIndex];
        $user->save();
        echo "$student[0]: DONE \n";
    } else {
        echo "$student[0]: FAILED \n";
    }
}
