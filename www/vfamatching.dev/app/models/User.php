<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends BaseModel implements UserInterface, RemindableInterface {
    protected function rules()
    {
        return array(
                'email'=> 'required|email|unique:users,email,'.$this->id,
                'lastLogin'=>'date',
                'role'=>'required|in:Admin,Fellow,Hiring Manager',
                'firstName'=>'required|max:100',
                'lastName'=>'required|max:100',
            );
    }

    protected function adminRules()
    {
        return $this->rules();
    }

	protected $guarded = array();

	 /* Required for Laravel Auth */
    protected $hidden = array('password');

    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

	public function profile()
	{
		if($this->role == 'Fellow') {
			return $this->hasOne('Fellow');
		} elseif($this->role == 'Hiring Manager') {
			return $this->hasOne('HiringManager');
		} elseif($this->role == 'Admin') {
			return $this->hasOne('Admin');
		} else {
			throw new Exception("Invalid User role!");
		}
	}

    public function login()
    {
        $this->lastLogin = Carbon::now()->toDateTimeString();
        $this->save();
    }

    public static function defaultPassword()
    {
        return "VFA_Matching!";
    }

    public static function roles()
    {
        return array('Fellow','Hiring Manager', 'Admin');
    }
    
    public function getRememberToken() 
	{ 
		return $this->remember_token; 
	} 

	public function setRememberToken($value) 
	{ 
		$this->remember_token = $value; 
	} 

	public function getRememberTokenName() 
	{ 
		return 'remember_token'; 
	}
}
