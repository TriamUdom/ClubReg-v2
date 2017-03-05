<?php

namespace App;


use Illuminate\Database\Eloquent\Model;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * App\Club
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $members
 * @mixin \Eloquent
 * @property string $id
 * @property string $name
 * @property string $english_name
 * @property bool $is_audition
 * @property bool $is_active
 * @property bool $subject_code
 * @property int $fix_teacher
 * @property string $president_title
 * @property string $president_fname
 * @property string $president_lname
 * @property string $adviser_title
 * @property string $adviser_fname
 * @property string $adviser_lname
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereAdviserFname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereAdviserLname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereAdviserTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereEnglishName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereFixTeacher($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereIsActive($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereIsAudition($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club wherePresidentFname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club wherePresidentLname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club wherePresidentTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereSubjectCode($value)
 */
class Club extends Model {
    public $incrementing = false;
    public $timestamps = false;
    
    public function members() {
        return $this->hasMany('App\User', 'club_id', 'id');
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
        if (file_exists(storage_path('app/FMOutput/' . $fileName . '.docx'))) {
            unlink(storage_path('app/FMOutput/' . $fileName . '.docx'));
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
        
        $templateProcessor->saveAs(storage_path('app/FMOutput/' . $fileName . '.docx'));
        
        return storage_path('app/FMOutput/' . $fileName . '.docx');
    }
    
    private function getAdviserName() {
        return $this->adviser_title . $this->adviser_fname . ' ' . $this->adviser_lname;
    }
    
    public function countMember():int {
        return $this->members()->count();
    }
    
    public function isAvailable():bool {
        // @todo
        return $this->countMember() < 200;
    }
}