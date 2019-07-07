<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class CleanBeforeWar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:cleanbfwar';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean before war table';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $db = DB::table('before_war')->get();
        foreach ($db as $row) {
            if (empty($row->student_id) or trim($row->firstname) != $row->firstname or empty($row->room) or empty($row->level)) {
                DB::table('before_war')->where('student_id', $row->student_id)->where('firstname', $row->firstname)->where('lastname', $row->lastname)->where('club_id', $row->club_id)->update([
                    'student_id' => empty(trim($row->student_id)) ? null : trim(str_replace(['๏ปฟ', '?'], '', $row->student_id)),
                    'title' => empty(trim($row->title)) ? null : trim($row->title),
                    'firstname' => trim($row->firstname),
                    'lastname' => empty(trim($row->lastname)) ? null : trim($row->lastname),
                    'level' => empty(trim($row->level)) ? null : trim(str_replace('ม.', '', $row->level)),
                    'room' => empty(trim($row->room)) ? null : trim($row->room),
                    'number' => empty(trim($row->number)) ? null : trim($row->number),
                    'club_id' => trim($row->club_id)
                ]);
                $this->info('Updated ' . $row->student_id . ': ' . $row->room);
            }
        }
    }
}
