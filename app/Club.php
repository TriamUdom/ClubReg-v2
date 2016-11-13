<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;

class Club extends Model {
    public $incrementing = false;
    public $timestamps = false;
    
    public function members() {
        return $this->hasMany('App\User', 'club', 'id');
    }
    
    /**
     * Create FM3304
     *
     * @param   int     semester that this FM3304 assigned to
     * @return  string  path to generated FM3304 file
     */
    public function createFM3304($semester) {
        $studentData = $this->members;
        
        $fileName = '[FM 33-04] ' . substr($this->id, -2) . '_' . $this->name;
        if (file_exists(public_path('FMOutput/' . $fileName . '.docx'))) {
            unlink(public_path('FMOutput/' . $fileName . '.docx'));
        }
        
        $templateProcessor = new TemplateProcessor(base_path('resources/FMtemplate/FM3304.docx'));
        
        $templateProcessor->setValue('clubName', htmlspecialchars($this->name));
        $templateProcessor->setValue('clubCode', htmlspecialchars($this->id));
        $templateProcessor->setValue('semester', htmlspecialchars($semester));
        $templateProcessor->setValue('operation_year', htmlspecialchars(config('core.current_year')));
        
        $studentCount = count($studentData);
        $templateProcessor->cloneRow('count', $studentCount);
        
        $studentLoop = 0;
        foreach ($studentData as $student) {
            $studentLoop++;
            $templateProcessor->setValue('count#' . $studentLoop, $studentLoop);
            
            $templateProcessor->setValue('tfname#' . $studentLoop, htmlspecialchars($student->title . $student->firstname));
            $templateProcessor->setValue('lname#' . $studentLoop, htmlspecialchars($student->lastname));
            
            $templateProcessor->setValue('class-room#' . $studentLoop, htmlspecialchars($student->level . '/' . $student->room));
        }
        
        $templateProcessor->setValue('adviserName', htmlspecialchars($this->getAdviserName()));
        
        $templateProcessor->saveAs(public_path('FMOutput/' . $fileName . '.docx'));
        
        return public_path('FMOutput/' . $fileName . '.docx');
    }
    
    private function getAdviserName() {
        return $this->adviser_title . $this->adviser_fname . ' ' . $this->adviser_lname;
    }
}