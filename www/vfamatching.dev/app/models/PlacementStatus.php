<?php

class PlacementStatus extends BaseModel {
    protected $table = 'placementStatuses';

    protected function rules()
    {
        return array(
            'status'=> 'required|in:Introduced,Contacted,Phone Interview Pending,Phone Interview Complete,On-site Interview Pending,On-site Interview Complete,Offer Extended,Offer Accepted,Conversation Closed',
            'eventDate'=>'date',
            'score'=>'in:1,2,3,4,5',
            'message'=>'max:280',
            'fromRole'=>'required|in:Admin,Fellow,Hiring Manager',
            'isRecent'=> 'required|in:0,1'
            );
    }

    protected function adminRules()
    {
        return $this->rules();
    }

    protected $guarded = array();

    public function fellow()
    {
        return $this->belongsTo('Fellow');
    }

    public function opportunity()
    {
        return $this->belongsTo('Opportunity');
    }

    public function percent()
    {
        $statuses = self::statuses();
        switch ($this->status) {
            case $statuses[0]: //Introduced
                return 1.0/8.0;
                break;
            case $statuses[1]: //Contacted
                return 2.0/8.0;
                break;
            case $statuses[2]: //Phone Interview Pending
                return 3.0/8.0;
                break;
            case $statuses[3]: //Phone Interview Complete
                return 4.0/8.0;
                break;
            case $statuses[4]: //On-site Interview Pending
                return 5.0/8.0;
                break;
            case $statuses[5]: //On-site Interview Complete
                return 6.0/8.0;
                break;
            case $statuses[6]: //Offer Extended
                return 7.0/8.0;
                break;
            case $statuses[7]: //Offer Accepted
                return 8.0/8.0;
                break;
            case $statuses[8]: //Conversation Closed
                return 0.0/8.0;
                break;
            default:
                throw new Exception("Invalid Status: " . $this->status);
                break;
        }
    }

    public function printWithDate()
    {
        if($this->status == self::statuses()[2]){
            return 'Phone Interview ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->eventDate)->diffForHumans();
        } else if($this->status == self::statuses()[4]) {
            return 'On-site Interview ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->eventDate)->diffForHumans();
        } else if($this->status == self::statuses()[6]) {
            return 'Offer Acceptance Deadline ' . Carbon::createFromFormat('Y-m-d H:i:s', $this->eventDate)->diffForHumans();
        } else {
            return $this->status;
        }
    }

    public static function hasPlacementStatus(Fellow $fellow, Opportunity $opportunity)
    {
        return (bool) $fellow->placementStatuses()->where('placementStatuses.opportunity_id', '=', $opportunity->id)->where('placementStatuses.isRecent', '=', true)->count();
    }

    public static function getRecentPlacementStatus(Fellow $fellow, Opportunity $opportunity)
    {
        if(self::hasPlacementStatus($fellow, $opportunity)){
            return PlacementStatus::where('fellow_id','=', $fellow->id)
                ->where('opportunity_id','=', $opportunity->id)
                ->where('isRecent', '=', true)
                ->first();
        } else {
            throw new Exception('This fellow does not have a Placemen Status with this opportunity');
        }
    }

    public static function getAllPlacementStatuses(Fellow $fellow, Opportunity $opportunity)
    {
        if(self::hasPlacementStatus($fellow, $opportunity)){
            return PlacementStatus::where('fellow_id','=', $fellow->id)
                ->where('opportunity_id','=', $opportunity->id)
                ->first();
        } else {
            throw new Exception('This fellow does not have a Placement Status with this opportunity');
        }
    }

    public static function statuses()
    {
        return array(
            'Introduced', //1/8
            'Contacted', //2/8
            'Phone Interview Pending', //3/8
            'Phone Interview Complete', //4/8
            'On-site Interview Pending', //5/8
            'On-site Interview Complete', //6/8
            'Offer Extended', //7/8
            'Offer Accepted', //8/8
            'Conversation Closed'); //0/8
    }

    public function history()
    {
        $fellow_id = $this->fellow_id;
        $opportunity_id = $this->opportunity_id;
        $history = PlacementStatus::where('fellow_id','=',$fellow_id)->where('opportunity_id','=',$opportunity_id)->
            orderBy('created_at', 'DESC')
            ->get();

        return $history;
    }

    public static function scores()
    {
        return array(
            5,
            4,
            3,
            2,
            1);
    }

    public static function generateReportData($limit)
    {
        $columnHeadings = array('Fellow', 'Opportunity', 'Company', 'Status', 'Created');
        $data = array();
        $data[0] = $columnHeadings;

        // $recentPlacementStatuses = PlacementStatus::where('isRecent','=',true)->orderBy('created_at', 'DESC')->take($limit)->get();
        $recentPlacementStatuses = PlacementStatus::orderBy('created_at', 'DESC')->take($limit)->get();
        $count = 1;
        foreach($recentPlacementStatuses as $placementStatus){
            $data[$count] = array();
            $fellow = $placementStatus->fellow;
            $opportunity = $placementStatus->opportunity;
            $company = $placementStatus->opportunity->company;
            foreach($columnHeadings as $key => $value){
                if($value == "Fellow"){
                    $data[$count][0] = '<a href="' . URL::to('fellows/' . $fellow->id) . '">' . $fellow->user->firstName . ' ' . $fellow->user->lastName . '</a>';
                } elseif($value == "Opportunity"){
                    $data[$count][1] = '<a href="' . URL::to('opportunities/' . $opportunity->id) . '">' . $opportunity->title . '</a>';
                } elseif($value == "Company"){
                    $data[$count][2] = '<a href="' . URL::to('companies/' . $company->id) . '">' . $company->name . '</a>';
                } elseif($value == "Status"){
                    $data[$count][3] = $placementStatus->status;
                } elseif($value == "Created"){
                    $data[$count][4] = $placementStatus->created_at;
                }               
            }
            $count += 1;
        }
        return $data;
    }
}