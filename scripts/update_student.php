<?php
$csv = file_get_contents("update_student.txt");

$lines = explode("\n", $csv);
$a = array();
foreach ($lines as $line) {
    $a[] = explode(",", $line);
}

foreach ($a as $student) {
    $user = \App\User::where(array(array('student_id', '=', $student[0])))->first();

    if (!is_null($user)) {
        $user->room=$student[4];
        $user->level=6;
        $user->number=$student[5];
        $user->save();
        echo "DONE \n";
    } else {
        $user = new \App\User();
        $user->student_id=$student[0];
        $user->password='';
        $user->level=6;
        $user->room=$student[4];
        $user->number=$student[5];
        $user->title=$student[1];
        $user->firstname=$student[2];
        $user->lastname=$student[3];
        $user->club_id='';
        $user->reason='';
        $user->comment='';
        $user->old_club_id='';
        $user->save();
        echo "ADDED \n";
    }
}
