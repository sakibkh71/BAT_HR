<?php
namespace App\Events;

use Auth;
use Illuminate\Support\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;
class AuditTrailEvent {
    protected $builder;
    protected $user;
    protected $time;
    protected $now;

    protected static $disk = 'audit';
    private static $storage;
    private static $table;
    private $start_date;
    private $end_date;
    public $logdata;

    public function __construct($logdata = null, $start_date = null, $end_date = null, $builder = null){
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->logdata = $logdata;
        if(!empty($builder)){
            $this->builder = $builder;
            $this->user = Auth::user()->id;
            $this->now = Carbon::now();
            $this->time = $this->now->toDateTimeString();
            self::$table = $builder->from;
        }
    }

    public static function build($builder){

        return new AuditTrailEvent(null, null, null, $builder);
    }

    public function update($update_data){
        $log_data['table'] = self::$table;
        $log_data['user'] = $this->user;
        $log_data['time'] = $this->time;
        $log_data['record']['old'] = (array)$this->builder->first();
        $updated = $this->builder->update($update_data);
        $log_data['record']['new'] = (array)$this->builder->first();
        if($updated) {
            $this->createUpdateLog($log_data);
        }
        return $updated;
    }
    public function delete(){
        $log_data['table'] = self::$table;
        $log_data['user'] = $this->user;
        $log_data['time'] = $this->time;
        $log_data['deleted'] = $this->builder->get()->toArray();
        $deleted = $this->builder->delete();
        if(!empty($deleted) || $deleted != 0){
            $this->createUpdateLog($log_data);
            $return = $deleted;
        }else{
            $return = 'failed';
        }

        return $return;
    }

    private function createUpdateLog($log_data){
        $storage = Storage::disk(self::$disk);
        $file_name = $this->now->format('m') . '.json';
        $log_path = $log_data['table'] . '\\' . $this->now->format('Y') . '\\';
        if (!$storage->has($log_path)) {
            $storage->makeDirectory($log_path, 0777, true, true);
        }
        $full_path = $log_path . $file_name;
        $previous_data = $storage->exists($full_path) ? json_decode($storage->get($full_path)) : [];
        array_push($previous_data, $log_data);
        $new_data = json_encode($previous_data);
        $storage->put($full_path, $new_data);
    }


    /*===============================================*/
    /*===============================================*/

    public static function table($table_name = ''){
        self::$storage = Storage::disk(self::$disk);
        self::$table = $table_name;
        return new static;
    }
    public function getFiles(){
        return self::$storage->allFiles(self::$table);
    }
    public function get($start_date = '', $end_date = ''){
        $this->start_date = $start_date != '' ? Carbon::parse($start_date) : '';
        $this->end_date = $end_date != '' ? Carbon::parse($end_date) : '';
        $allfiles = $this->getFiles();
        if(empty($this->start_date) && empty($this->end_date)){
            //----all files
            $requestfiles = $allfiles;
        }elseif (!empty($this->start_date) && empty($this->end_date)){
            //----only for selected dates
            $requestfiles[] = self::$table.'/'.$this->start_date->format('Y').'/'.$this->start_date->format('m').'.json';
        }elseif (empty($this->start_date) && !empty($this->end_date)){
            //----all before end dates
            foreach ($allfiles as $i => $allfile){
                $file_segmants = explode('/', $allfile);
                if($file_segmants[1] > $this->end_date->format('Y')){
                    unset($allfiles[$i]);
                }else{
                    if(explode('.',$file_segmants[2])[0] > $this->end_date->format('m')){
                        unset($allfiles[$i]);
                    }
                }
            }
        }else{
            //----between 2 dates
            foreach ($allfiles as $i => $allfile){
                $file_segmants = explode('/', $allfile);
                if($file_segmants[1] < $this->start_date->format('Y') || $file_segmants[1] > $this->end_date->format('Y')){
                    unset($allfiles[$i]);
                }else{
                    $name_segmants = explode('.', $file_segmants[2])[0];
                    if($name_segmants < $this->start_date->format('m') || $name_segmants > $this->end_date->format('m')){
                        unset($allfiles[$i]);
                    }
                }
            }
        }
        $data = $this->readAuditLog(array_values($allfiles));
        return new AuditTrailEvent($data, $this->start_date, $this->end_date);
    }
    public function readAuditLog($requestfiles){
        $storage = self::$storage;
        $data = array_map(function ($file) use ($storage){
            return $storage->exists($file) ? json_decode($storage->get($file)) : null;
        }, $requestfiles);
        return json_encode($data);
    }
    public function toArray(){
        return json_decode($this->logdata, True);
    }
    public function logdata($clean = 0){
        $requested_data = json_decode($this->logdata, True);
        $file_data = [];
        if(!empty($requested_data)){
            foreach ($requested_data as $it_1){
                foreach ($it_1 as $key => $it_2){
                    $audit_data_time = Carbon::parse($it_2['time'])->toDateString();
                    if(is_array($it_2)){
                        if(!empty($this->start_date) && !empty($this->end_date)){
                            $start_date = $this->start_date->toDateString();
                            $end_date = $this->end_date->toDateString();
                            if(($audit_data_time >= $start_date) && ($audit_data_time <= $end_date)){
                                $file_data[$key] = self::createLogArr($it_2, $clean);
                            }
                        }elseif (!empty($this->start_date) && empty($this->end_date)){
                            $start_date = $this->start_date->toDateString();
                            if($audit_data_time == $start_date){
                                $file_data[$key] = self::createLogArr($it_2, $clean);
                            }
                        }elseif (empty($this->start_date) && !empty($this->end_date)){
                            $end_date = $this->end_date->toDateString();
                            if($audit_data_time <= $end_date){
                                $file_data[$key] = self::createLogArr($it_2, $clean);
                            }
                        }else{
                            $file_data[$key] = self::createLogArr($it_2, $clean);
                        }
                    }
                }
            }
        }
        return $file_data;
    }
    public static function createLogArr($it_2, $clean){
        $data = $it_2;
        if($clean){
            if(!empty($it_2['record'])){
                $i['new'] = array_diff($it_2['record']['new'], $it_2['record']['old']);
                $i['old'] = array_diff($it_2['record']['old'], $it_2['record']['new']);
                $data['record'] = $i;
            }
        }
        return $data;
    }
}