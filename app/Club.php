<?php

namespace App;

use App\Exceptions\UserFriendlyException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\TemplateProcessor;

/**
 * App\Club
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[]     $members
 * @mixin \Eloquent
 * @property string                                                        $id
 * @property string                                                        $name
 * @property string                                                        $english_name
 * @property bool                                                          $is_audition
 * @property bool                                                          $is_active
 * @property int                                                           $subject_code
 * @property string                                                        $president_title
 * @property string                                                        $president_fname
 * @property string                                                        $president_lname
 * @property string                                                        $adviser_title
 * @property string                                                        $adviser_fname
 * @property string                                                        $adviser_lname
 * @property int                                                           $max_member
 * @property string                                                        $description
 * @property array                                                         $user_id List of admin usernames, separated by comma
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
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereMaxTeacher($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereMaxMember($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereUserId($value)
 * @property string                                                        $audition_location
 * @property string                                                        $audition_time
 * @property string                                                        $audition_instruction
 * @property string                                                        $location
 * @property string                                                        $president_phone
 * @property string                                                        $adviser_phone
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Audition[] $auditions
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereAdviserPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereAuditionLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club whereLocation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Club wherePresidentPhone($value)
 */
class Club extends Model
{
    public $incrementing = false;
    
    protected $fillable = [
        'president_title',
        'president_fname',
        'president_lname',
        'adviser_title',
        'adviser_fname',
        'adviser_lname',
        'president_phone',
        'adviser_phone',
        'description',
        'audition_location',
        'audition_time',
        'audition_instruction',
        'location'
    ];
    
    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_audition' => 'boolean',
        'is_active' => 'boolean',
        'user_id' => 'array'
    ];
    
    public static function currentPresident()
    {
        if ($user = self::find(session('president'))) {
            /** @var $user Club */
            return $user;
        } else {
            $e = new UserFriendlyException('President not logged in');
            $e->setDescription('ประธานชมรมยังไม่ได้เข้าสู่ระบบ');
            throw $e;
        }
    }

    /**
     * Get all clubs
     *
     * @return array
     */
    public static function fetchAllClubs(): array
    {
        $clubs = self::where('is_active', true)->get()->reject(function (Club $item) {
            return !$item->isAvailable();
        })->all();
        $list = array();
        foreach ($clubs as $club) {
            $list [$club->name] = $club->id;
        }

        return $list;
    }
    
    /**
     * Get all club open for audition
     *
     * @return array
     */
    public static function fetchAuditionClubs(): array
    {
        $clubs = self::where('is_audition', true)->where('is_active', true)->get()->reject(function (Club $item) {
            return !$item->isAvailable();
        })->all();
        $list = array();
        foreach ($clubs as $club) {
            $list [$club->name] = $club->id;
        }
        
        return $list;
    }
    
    /**
     * Get all club available in war
     *
     * @return array
     */
    public static function fetchWarClubs(): array
    {
        $clubs = self::where('is_audition', false)->where('is_active', true)->get()->reject(function (Club $item) {
            return !$item->isAvailable();
        })->all();
        $list = array();
        foreach ($clubs as $club) {
            $list [$club->name] = $club->id;
        }
        
        return $list;
    }
    
    public function members()
    {
        return $this->hasMany('App\User', 'club_id', 'id');
    }
    
    public function auditions()
    {
        return $this->hasMany('App\Audition', 'club_id', 'id');
    }
    
    /**
     * Create FM3304
     *
     * @param   int     semester that this FM3304 assigned to
     * @return  string  path to generated FM3304 file
     */
    public function createFM3304($semester)
    {
        return self::generateFM3304($this->members()->orderBy('level', 'ASC')->orderBy('room', 'ASC')->get(), $semester);
    }
    
    public function generateFM3304(Collection $studentData, $semester)
    {
        // StudentData: title firstname lastname level room
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
    
    public function generateFM3301(Collection $studentData, int $adviserCount)
    {
        $criterionCount = $adviserCount * 30;
        
        $fileName = '[FM 33-01] ' . substr($this->id, -2) . '_' . $this->name;
        if (file_exists(public_path('FMOutput/' . $fileName . '.docx'))) {
            unlink(public_path('FMOutput/' . $fileName . '.docx'));
        }
        
        $templateProcessor = new TemplateProcessor(resource_path('FMtemplate/FM3301.docx'));
        
        $templateProcessor->setValue('clubName', htmlspecialchars(str_replace('ชมรม', '', $this->name)));
        $templateProcessor->setValue('clubCode', htmlspecialchars($this->id));
        $templateProcessor->setValue('adviserCount', htmlspecialchars($adviserCount));
        $templateProcessor->setValue('criterionCount', htmlspecialchars($criterionCount));
        
        $class4StudentCount = 0;
        $class5StudentCount = 0;
        $class6StudentCount = 0;
        $studentCount = count($studentData);
        
        for ($i = 0; $i < $studentCount; $i++) {
            switch ($studentData[$i]->level) {
                case 4:
                    $class4StudentCount += 1;
                    break;
                case 5:
                    $class5StudentCount += 1;
                    break;
                case 6:
                    $class6StudentCount += 1;
                    break;
                default:
                    throw new UserFriendlyException("Class " . $studentData[$i]->level . " does not exists.");
                    break;
            }
        }
        
        $lessThanCriterion = 0;
        $moreThanCriterion = 0;
        
        if ($studentCount > $adviserCount * 30) {
            $moreThanCriterion = $studentCount - $adviserCount * 30;
        } else {
            if ($studentCount < $adviserCount * 30) {
                $lessThanCriterion = $adviserCount * 30 - $studentCount;
            }
        }
        
        $templateProcessor->setValue('totalStudentCount', htmlspecialchars($studentCount));
        $templateProcessor->setValue('class4StudentCount', htmlspecialchars($class4StudentCount));
        $templateProcessor->setValue('class5StudentCount', htmlspecialchars($class5StudentCount));
        $templateProcessor->setValue('class6StudentCount', htmlspecialchars($class6StudentCount));
        $templateProcessor->setValue('lessThanCriterion', htmlspecialchars($lessThanCriterion));
        $templateProcessor->setValue('moreThanCriterion', htmlspecialchars($moreThanCriterion));
        
        $templateProcessor->cloneRow('count', $studentCount);
        
        for ($j = 0; $j < $studentCount; $j++) {
            $k = $j + 1;
            $templateProcessor->setValue('count#' . $k, $k);
            
            $templateProcessor->setValue('tfname#' . $k, htmlspecialchars($studentData[$j]->title . $studentData[$j]->firstname));
            $templateProcessor->setValue('lname#' . $k, htmlspecialchars($studentData[$j]->lastname));
            
            $templateProcessor->setValue('class#' . $k, htmlspecialchars('ม.' . $studentData[$j]->level));
            $templateProcessor->setValue('room#' . $k, htmlspecialchars((int)$studentData[$j]->room));
        }
        
        $templateProcessor->setValue('operation_year', htmlspecialchars(config('core.current_year')));
        
        $templateProcessor->setValue('presidentName', htmlspecialchars($this->getPresidentName()));
        $templateProcessor->setValue('adviserName', htmlspecialchars($this->getAdviserName()));
        
        $templateProcessor->saveAs(storage_path('app/FMOutput/' . $fileName . '.docx'));
        
        return storage_path('app/FMOutput/' . $fileName . '.docx');
    }
    
    public function getAdviserName()
    {
        return $this->adviser_title . $this->adviser_fname . ' ' . $this->adviser_lname;
    }
    
    public function getPresidentName()
    {
        return $this->president_title . $this->president_fname . ' ' . $this->president_lname;
    }
    
    public function countMember(): int
    {
        return $this->members()->count();
    }
    
    public function isAvailable(bool $asLevel = false)
    {
        $memberNumber = $this->countMember();
        if (!$this->is_active) {
            return false;
        } elseif ($asLevel) {
            // 0: Full, 1: Almost, 2: Available
            if ($memberNumber >= $this->max_member) {
                return 0;
            } elseif ($memberNumber > $this->max_member * 0.9) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return $memberNumber < $this->max_member;
        }
    }
    
    public function isAvailableForConfirm()
    {
        // 65%/35% Available for existing member
        return $this->members()->count() < ($this->max_member * ($this->is_audition ? 0.65 : 0.35)) and $this->isAvailable();
    }
    
    public function isAvailableForLevel($level)
    {
        /*
        if (!$this->is_active) {
            return false;
        } elseif ($level == 4) {
            return $this->isAvailable();
        } else {
            // Reserve 20% for M4
            return $this->members()->where('level', '!=', 4)->count() < $this->max_member * 0.8 AND $this->isAvailable();
        }
        */
        if (!$this->is_active) {
            return false;
        } elseif (!$this->is_audition && Helper::isRound(Helper::Round_Register)) {
            switch ($level) {
                case 4:
                    return $this->members()->where('reason', '=', 'WAR')->where('level', 4)->count() < $this->max_member * 0.35 and $this->isAvailable();
                case 5:
                case 6:
                    return $this->members()->where('reason', '=', 'WAR')->where('level', '!=', 4)->count() < $this->max_member * 0.2 and $this->isAvailable();
            }
        }
        return $this->isAvailable();
    }

    public function seatsAvailable($level)
    {
        if (Helper::isRound(Helper::Round_Register)) {
            if ($this->is_audition) {
                return $this->max_member - $this->countMember();
            } else {
                switch ($level) {
                    case 4:
                        return floor($this->max_member * 0.35) - $this->members()->where('reason', '=', 'WAR')->where('level', 4)->count();
                    case 5:
                    case 6:
                        return floor($this->max_member * 0.2) - $this->members()->where('reason', '=', 'WAR')->where('level', '!=', 4)->count();
                }
            }
        } elseif (Helper::isRound(Helper::Round_Confirm)) {
            return floor($this->max_member * ($this->is_audition ? 0.65 : 0.35)) - $this->members()->count();
        } else { //Glean
            return $this->max_member - $this->countMember();
        }
    }
}
