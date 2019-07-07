<?php
$csv = file_get_contents("cld.txt");

$lines = explode("\n", $csv);
$a = array();
foreach ($lines as $line) {
    $a[] = explode(",", $line);
}

foreach ($a as $old_member) {
    $user = \App\User::where(array(
        array('firstname', '=', $old_member[0]),
        array('lastname', '=', $old_member[1])))->first();

    if (!is_null($user)) {
        $user->club_id = 'ก30920-1';
        $user->save();
        echo "DONE \n";
    }
}
