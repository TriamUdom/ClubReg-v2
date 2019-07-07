<?php

namespace App\Console\Commands;

use App\Club;
use Illuminate\Console\Command;

class GenerateList extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temp:gendoc {doc : 3301 or 3304}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate club document (before_war)';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $clubId = 'ก' . $this->ask('What is your club id (5 digits)?');
        $docType = $this->argument('doc') ?? $this->ask('Which document do you want? (3301 / 3304)');
        //$studentData = collect(\DB::table('before_war')->where('club_id', $clubId)->orderby('level')->orderBy('room')->orderBy('student_id')->get());
        $studentData = Club::find($clubId)->members()->orderBy('level')->orderBy('room')->orderBy('student_id')->get();
        if ($docType == '3301') {
            $this->info('Generating FM 33-01: Member List Report');
            $filePath = Club::find($clubId)->generateFM3301($studentData, $this->ask('How many adviser are in your club?'));
        } else {
            $this->info('Generating FM 33-04: Timestamp');
            $filePath = Club::find($clubId)->generateFM3304($studentData, 1);
        }
        $this->info('Saved at ' . $filePath);
    }
}
