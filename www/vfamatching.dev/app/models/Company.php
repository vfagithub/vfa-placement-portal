<?php

class Company extends BaseModel {
    protected $table = 'companies';

	protected function rules()
    {
        return array(
            'name'=>'required|max:280|unique:companies,name,'.$this->id,
            'city'=>'required|max:280',
            'url'=>'required|url',
            'twitterPitch'=>'required|max:140',
            'bio'=>'required|max:1400',
            'visionAnswer'=>'max:280',
            'needsAnswer'=>'max:280',
            'teamAnswer'=>'required|max:280',
            'employees'=>'required|integer',
            'yearFounded'=>'required|digits:4',
            'twitterHandle'=>'max:15',
            'isPublished'=> 'required|in:0,1',
            'hasFellow'=> 'required|in:0,1'
        );
    }

    protected function adminRules()
    {
        return array(
            'name'=>'max:280|unique:companies,name,'.$this->id,
            'city'=>'max:280',
            'url'=>'url',
            'twitterPitch'=>'max:140',
            'bio'=>'max:1400',
            'visionAnswer'=>'max:280',
            'needsAnswer'=>'max:280',
            'teamAnswer'=>'max:280',
            'employees'=>'integer',
            'yearFounded'=>'digits:4',
            'twitterHandle'=>'max:15',
            'isPublished'=> 'in:0,1',
            'hasFellow'=> 'in:0,1'
        );
    }

    protected $guarded = array();

	public function mediaLinks()
    {
        return $this->belongsToMany('MediaLink');
    }

    public function opportunities()
    {
        return $this->hasMany('Opportunity');
    }

    public function hiringManagers()
    {
        return $this->hasMany('HiringManager');
    }

    public function adminNotes()
    {
        return $this->belongsToMany('AdminNote', 'adminNote_company', 'company_id', 'adminNote_id');
    }

    public function fellowNotes()
    {
        return $this->belongsToMany('FellowNote', 'fellowNote_company', 'company_id', 'fellowNote_id');
    }

    public static function dropdownOfAllNames()
    {
        $html = '<div class="form-group" id="company-picker"><label>Which Company?</label><div class="input-group"><span class="input-group-addon"><i class="fa fa-building-o"></i></span><select name="company" class="form-control company-dropdown required">';
        $html .= '<option value=""></option>';
        $html .= '<option value="0">New Company</option>';
        foreach(Company::all() as $company){
            $html .= '<option value="'.$company->id.'">'.$company->name.'</option>';
        }
        $html .= '</div></div>';
        return $html;
    }

    public function isProfileComplete(){
        if(empty($this->name) ||
            empty($this->city) ||
            empty($this->url) ||
            empty($this->twitterPitch) ||
            // empty($this->visionAnswer) ||
            // empty($this->needsAnswer) ||
            empty($this->bio) ||
            empty($this->teamAnswer) ||
            empty($this->employees) ||
            empty($this->yearFounded)){
            return false;
        } else {
            return true;
        }
    }

    public function canViewContactInfo()
    {
        if(count($this->hiringManagers) > 0){
            if(Auth::check()){
                if(Auth::user()->role == "Admin"){
                    return true;
                } elseif(Auth::user()->role == "Fellow" && Auth::user()->profile->isIntroduced($this)){
                    return true;
                } elseif(Auth::user()->role == "Hiring Manager" && $this->id == Auth::user()->profile->company->id){
                    return true;
                }
            }
        }
        return false;
    }

    public function hasPublishedOpportunities()
    {
        if($this->opportunities()->where('opportunities.isPublished','=',true)->count() < 1){
            return false;
        }
        return true;
    }

    public static function generateReportData()
    {
        if(Auth::user()->role == "Admin"){
            $columnHeadings = array_merge(array('Company', 'Opportunity', 'Average Feedback', 'Pitch:Under Review', 'Pitch:Waitlisted', 'Pitch:Approved'), PlacementStatus::statuses());
        } else {
            $columnHeadings = array_merge(array('Company', 'Opportunity', 'Pitch:Under Review', 'Pitch:Waitlisted', 'Pitch:Approved'), PlacementStatus::statuses());
        }
        $data = array();
        $data[0] = $columnHeadings;

        $publishedCompanies = Company::where('isPublished','=',true)->get();
        $count = 1;
        foreach($publishedCompanies as $company){
            foreach($company->opportunities()->where('isPublished','=',true)->get() as $opportunity){
                $data[$count] = array();
                foreach($columnHeadings as $key => $value){
                    if($value == "Company"){
                        $data[$count][0] = '<a href="' . URL::to('companies/' . $company->id) . '">' . $company->name . '</a>';
                    } else if($value == "Opportunity"){
                        $data[$count][1] = '<a href="' . URL::to('opportunities/' . $opportunity->id) . '">' . $opportunity->title . '</a>';
                    } else if($value == "Average Feedback" && Auth::user()->role == "Admin"){
                        $data[$count][2] = $opportunity->averagePlacementStatusFeedbackScore();
                    } else {
                        $data[$count][$key] = 0;
                    }                
                }
                foreach($opportunity->pitches as $pitch){
                    $key = array_search("Pitch:" . $pitch->status, $columnHeadings);
                    $data[$count][$key] += 1;
                }
                foreach($opportunity->placementStatuses()->where('isRecent','=',true)->get() as $placementStatus){
                    $data[$count][array_search($placementStatus->status, $columnHeadings)] += 1;
                }
                $count += 1;
            }
        }
        return $data;
    }
}
