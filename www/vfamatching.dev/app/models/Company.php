<?php

class Company extends BaseModel {
	protected function rules()
    {
        return array(
            'name'=>'required|max:280|unique:companies,name,'.$this->id,
            'city'=>'required|max:280',
            'url'=>'required|url',
            'tagline'=>'required|max:140',
            'visionAnswer'=>'required|max:280',
            'needsAnswer'=>'required|max:280',
            'teamAnswer'=>'required|max:280',
            'employees'=>'required|integer',
            'yearFounded'=>'required|integer',
            'twitterHandle'=>'max:15',
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
        return $this->belongsToMany('adminNote');
    }
}
