<?php
$csv = file_get_contents("fix_club.txt");

$lines = explode("\n", $csv);
$a = array();
foreach ($lines as $line) {
    $a[] = explode(",", $line);
}

foreach ($a as $student){
    $user = \App\User::where([['student_id', '=', $student[0]]])->first();

    if (!is_null($user)){
        $user->club_id = 'ก30914';
        $user->save();
        echo "DONE \n";
    }
}