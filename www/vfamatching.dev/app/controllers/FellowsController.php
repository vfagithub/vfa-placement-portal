<?php

class FellowsController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('adminOrHiringManager', array('only' => array('index')));
        $this->beforeFilter('admin', array('only' => array('publish', 'unpublish')));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $sort = (!is_null(Input::get('sort')) ? Input::get('sort') : 'firstName'); //default to fellow first name
        $order = (!is_null(Input::get('order')) ? Input::get('order') : 'asc'); //default to asc
        $search = (!is_null(Input::get('search')) ? Input::get('search') : ''); //default to empty string
        $limit = (!is_null(Input::get('limit')) ? Input::get('limit') : 20); //default to 5
        $fellows = Fellow::select('fellows.*', 'users.firstName', 'users.lastName')
            ->join('users', 'fellows.user_id', '=', 'users.id')
            ->leftJoin('fellowSkills', 'fellows.id', '=', 'fellowSkills.fellow_id');
        if($search != ''){
            $searchTerms = explode(' ', $search);
            foreach($searchTerms as $searchTerm){
                if(strcasecmp($searchTerm, "and")){
                    $fellows = $fellows->where(function ($query) use ($searchTerm){
                        $query->where('bio', 'LIKE', "%$searchTerm%")
                        ->orWhere('school', 'LIKE', "%$searchTerm%")
                        ->orWhere('major', 'LIKE', "%$searchTerm%")
                        ->orWhere('degree', 'LIKE', "%$searchTerm%")
                        ->orWhere('graduationYear', '=', $searchTerm)
                        ->orWhere('hometown', 'LIKE', "%$searchTerm%")
                        ->orWhere('users.firstName', 'LIKE', "%$searchTerm%")
                        ->orWhere('users.lastName', 'LIKE', "%$searchTerm%")
                        ->orWhere('fellowSkills.skill', 'LIKE', "%$searchTerm%");
                    });
                }
            }
        }
        $fellows = $fellows->orderBy($sort, $order)->groupBy('fellows.id')->having('isPublished','=',true)->paginate($limit);
        $pills  = array();
            array_push($pills, new Pill("First Name", array(
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'users.firstName', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'users.firstName', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Last Name", array(
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'users.lastName', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'users.lastName', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Major", array(
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'major', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'major', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("School", array(
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'school', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'school', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Hometown", array(
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'hometown', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'fellows.index', array('sort' => 'hometown', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Date Added", array(
                    new DropdownItem("Oldest first", URL::route( 'fellows.index', array('sort' => 'created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit))),
                    new DropdownItem("Newest first", URL::route( 'fellows.index', array('sort' => 'created_at', 'order' => 'desc', 'search' => $search, 'limit' => $limit)))
                )));
        return View::make('fellows.index', array('total' => Fellow::Where('isPublished', '=', true)->count(), 'fellows' => $fellows, 'sort' => $sort, 'order' => $order, 'search' => $search, 'limit' => $limit, 'pills' => $pills));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('fellows.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $authenticatedUser = Auth::user();
        $authenticatedUser->firstName = Input::get('firstName');
        $authenticatedUser->lastName = Input::get('lastName');
        $authenticatedUser->email = Input::get('email');

        $newFellow = new Fellow();
        $newFellow->user_id = Auth::user()->id;
        $newFellow->isPublished = 1;
        $newFellow->isRemindable = 1;
        $newFellow->bio = Input::get('bio');
        $newFellow->school = Input::get('school');
        $newFellow->major = Input::get('major');
        $newFellow->degree = Input::get('degree');
        $newFellow->graduationYear = Parser::stringToInteger(Input::get('graduationYear'));
        $newFellow->hometown = Input::get('hometown');
        $newFellow->phoneNumber = Parser::stringToInteger(Input::get('phoneNumber'));

        if (Input::hasFile('displayPicture'))
        {
            //process file input
            //TODO: Validate that this is an image
            $newName = Uploader::processInputFilename(Input::file('displayPicture')->getClientOriginalName());
            try{
                Input::file('displayPicture')->move(public_path() . Config::get('upload.directory'), $newName);
            } catch (FileException $e) {
                return Redirect::back()->with('flash_error', "Your file could not be uploaded. Please try again")->withInput();
            }
            $newFellow->displayPicturePath = Config::get('upload.directory') . '/' . $newName;
        }

        if (Input::hasFile('resume'))
        {
            //process file input
            //TODO: Validate that this is a pdf
            $newName = Uploader::processInputFilename(Input::file('resume')->getClientOriginalName());
            try{
                Input::file('resume')->move(public_path() . Config::get('upload.directory'), $newName);
            } catch (FileException $e) {
                return Redirect::back()->with('flash_error', "Your file could not be uploaded. Please try again")->withInput();
            }
            $newFellow->resumePath = Config::get('upload.directory') . '/' . $newName;
        }

        // *** Commented out in lieu of Job Types ***
        // $fellowSkills = array();
        // if (Input::has('skills'))
        // {
        //     //TODO: Validate that this is a pdf
        //     $skills = explode(',', Input::get('skills'));
        //     //trim each skill
        //     array_walk($skills, function(&$value, $key){
        //         $value = trim($value);
        //     });
        //     foreach($skills as $skill){
        //         $fellowSkill = new FellowSkill();
        //         $fellowSkill->skill = $skill;
        //         array_push($fellowSkills, $fellowSkill);
        //     }
        // }

        $fellowSkills = array();
        if (Input::has('jobType')){
            foreach(Input::get('jobType') as $jobType){
                $fellowSkill = new FellowSkill();
                $fellowSkill->skill = $jobType;
                array_push($fellowSkills, $fellowSkill);
            }
        }

        try {
            $authenticatedUser->save();
            $newFellow->save();
            foreach($fellowSkills as $fellowSkill){
                $fellowSkill->fellow_id = $newFellow->id;
                $fellowSkill->save();
            }
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages())->withInput();
        }

        return Redirect::route('dashboard')->with('flash_success', 'Profile successfully updated.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if(Auth::user()->role == "Fellow"){
            if(Auth::user()->profile->id != $id){
                return Redirect::route('dashboard')->with('flash_error', "You don't have the necessary permissions to do that!");
            }
        }
        try{
            $fellow = Fellow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Fellow not found!');
        }
        return View::make('fellows.show', array('fellow' => $fellow));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        try{
            $fellow = Fellow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Fellow not found!');
        }
        return View::make('fellows.edit', array('fellow' => $fellow));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $authenticatedUser = Auth::user();
        $authenticatedUser->firstName = Input::get('firstName');
        $authenticatedUser->lastName = Input::get('lastName');
        $authenticatedUser->email = Input::get('email');

        try{
            $fellow = Fellow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Trying to update a fellow that doesn\'t exist!');
        }
        $fellow->user_id = Auth::user()->id;
        $fellow->isPublished = 1;
        $fellow->isRemindable = 1;
        $fellow->bio = Input::get('bio');
        $fellow->school = Input::get('school');
        $fellow->major = Input::get('major');
        $fellow->degree = Input::get('degree');
        $fellow->graduationYear = Parser::stringToInteger(Input::get('graduationYear'));
        $fellow->hometown = Input::get('hometown');
        $fellow->phoneNumber = Parser::stringToInteger(Input::get('phoneNumber'));

        if (Input::hasFile('displayPicture'))
        {
            //process file input
            //TODO: Validate that this is an image
            $newName = Uploader::processInputFilename(Input::file('displayPicture')->getClientOriginalName());
            try{
                Input::file('displayPicture')->move(public_path() . Config::get('upload.directory'), $newName);
            } catch (FileException $e) {
                return Redirect::back()->with('flash_error', "Your file could not be uploaded. Please try again")->withInput();
            }
            $fellow->displayPicturePath = Config::get('upload.directory') . '/' . $newName;
        }

        if (Input::hasFile('resume'))
        {
            //process file input
            //TODO: Validate that this is a pdf
            $newName = Uploader::processInputFilename(Input::file('resume')->getClientOriginalName());
            try{
                Input::file('resume')->move(public_path() . Config::get('upload.directory'), $newName);
            } catch (FileException $e) {
                return Redirect::back()->with('flash_error', "Your file could not be uploaded. Please try again")->withInput();
            }
            $fellow->resumePath = Config::get('upload.directory') . '/' . $newName;
        }

        // $fellowSkills = array();
        // if (Input::has('skills'))
        // {
        //     //TODO: Validate that this is a pdf
        //     $skills = explode(',', Input::get('skills'));
        //     //trim each skill
        //     array_walk($skills, function(&$value, $key){
        //         $value = trim($value);
        //     });
        //     foreach($skills as $skill){
        //         $fellowSkill = new FellowSkill();
        //         $fellowSkill->skill = $skill;
        //         array_push($fellowSkills, $fellowSkill);
        //     }
        // }

        $fellowSkills = array();
        if (Input::has('jobType')){
            foreach(Input::get('jobType') as $jobType){
                $fellowSkill = new FellowSkill();
                $fellowSkill->skill = $jobType;
                array_push($fellowSkills, $fellowSkill);
            }
        }

        try {
            $authenticatedUser->save();
            $fellow->save();
            foreach($fellow->fellowSkills as $fellowSkill){
                $fellowSkill->delete();
            }
            foreach($fellowSkills as $fellowSkill){
                $fellowSkill->fellow_id = $fellow->id;
                $fellowSkill->save();
            }
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages())->withInput();
        }

        return Redirect::route('fellows.show', $fellow->id)->with('flash_success', 'Profile successfully updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function publish($id)
    {
        try{
            $fellow = Fellow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Fellow not found!');
        }
        $fellow->isPublished = true;
        $fellow->save();
        return Redirect::back()->with('flash_success', "Fellow published");
    }

    public function unpublish($id)
    {
        try{
            $fellow = Fellow::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Fellow not found!');
        }
        $fellow->isPublished = false;
        $fellow->save();
        return Redirect::back()->with('flash_success', "Fellow unpublished");
    }

}
