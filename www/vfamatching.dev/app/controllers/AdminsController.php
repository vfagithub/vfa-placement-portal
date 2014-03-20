<?php

class AdminsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('admins.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('admins.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// die('Data Posted!<br/>' . json_encode(Input::all()));
		$authenticatedUser = Auth::user();
        $authenticatedUser->firstName = Input::get('firstName');
        $authenticatedUser->lastName = Input::get('lastName');
        $authenticatedUser->email = Input::get('email');

        $newAdmin = new Admin();
        $newAdmin->phoneNumber = Parser::stringToInteger(Input::get('phoneNumber'));
        $newAdmin->user_id = $authenticatedUser->id;

        try {
            $authenticatedUser->save();
            $newAdmin->save();
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages())->withInput();
        }

        return Redirect::route('dashboard')->with('flash_success', 'Profile successfully updated!');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('admins.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('admins.edit');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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

	public function archive()
	{
		if(Input::has('type')){
			$type = Input::get('type');
			if(Input::get('type') == "Fellow"){
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
		        $fellows = $fellows->orderBy($sort, $order)->groupBy('fellows.id')->having('isPublished','=',false)->paginate($limit);
		        $pills  = array();
		            array_push($pills, new Pill("First Name", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'users.firstName', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'users.firstName', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Last Name", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'users.lastName', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'users.lastName', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Major", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'major', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'major', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("School", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'school', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'school', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Hometown", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'hometown', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'hometown', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Date Added", array(
		                    new DropdownItem("Oldest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type))),
		                    new DropdownItem("Newest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)))
		                )));
		        return View::make('archives.fellows', array('total' => Fellow::Where('isPublished', '=', false)->count(), 'archivedFellows' => $fellows, 'sort' => $sort, 'order' => $order, 'search' => $search, 'limit' => $limit, 'pills' => $pills, 'type' => $type, 'archive' => true));	
			} elseif(Input::get('type') == "Opportunity") {
				$sort = (!is_null(Input::get('sort')) ? Input::get('sort') : 'companies.name'); //default to company name
		        $order = (!is_null(Input::get('order')) ? Input::get('order') : 'asc'); //default to asc
		        $search = (!is_null(Input::get('search')) ? Input::get('search') : ''); //default to empty string
		        $limit = (!is_null(Input::get('limit')) ? Input::get('limit') : 20); //default to 5
		        $opportunities = Opportunity::select('opportunities.*', 'companies.name')
		            ->join('companies', 'opportunities.company_id', '=', 'companies.id')
		            ->leftJoin('opportunityTags', 'opportunities.id', '=', 'opportunityTags.opportunity_id');
		        if($search != ''){
		            $searchTerms = explode(' ', $search);
		            foreach($searchTerms as $searchTerm){
		            	if(strcasecmp($searchTerm, "and")){
			                $opportunities = $opportunities->where(function ($query) use ($searchTerm){
			                	$query->Where('title', 'LIKE', "%$searchTerm%")
			                    ->orWhere('companies.name', 'LIKE', "%$searchTerm%")
			                    ->orWhere('teaser', 'LIKE', "%$searchTerm%")
			                    ->orWhere('description', 'LIKE', "%$searchTerm%")
			                    ->orWhere('responsibilitiesAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('skillsAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('developmentAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('opportunities.city', 'LIKE', "%$searchTerm%")
			                    ->orWhere('opportunityTags.tag', 'LIKE', "%$searchTerm%");
			                });
		            	}
		            }
		        }
		        $opportunities = $opportunities->orderBy($sort, $order)->groupBy('opportunities.id')->having('isPublished', '=', false)->paginate($limit);
		        $pills  = array();
		            array_push($pills, new Pill("Title", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'title', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'title', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Company", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'companies.name', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'companies.name', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("City", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'city', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'city', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Date Added", array(
		                    new DropdownItem("Oldest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type))),
		                    new DropdownItem("Newest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)))
		                )));
		        return View::make('archives.opportunities', array('total' => Opportunity::Where('isPublished', '=', false)->count(), 'archivedOpportunities' => $opportunities, 'sort' => $sort, 'order' => $order, 'search' => $search, 'limit' => $limit, 'pills' => $pills, 'type' => $type, 'archive' => true));
				// return View::make('archives.opportunities');		
			}  elseif(Input::get('type') == "Company") {
				$sort = (!is_null(Input::get('sort')) ? Input::get('sort') : 'name'); //default to name
		        $order = (!is_null(Input::get('order')) ? Input::get('order') : 'asc'); //default to asc
		        $search = (!is_null(Input::get('search')) ? Input::get('search') : ''); //default to empty string
		        $limit = (!is_null(Input::get('limit')) ? Input::get('limit') : 20); //default to 5
		        $companies = Company::select('companies.*');
		        if($search != ''){
		            $searchTerms = explode(' ', $search);
		            foreach($searchTerms as $searchTerm){
		            	if(strcasecmp($searchTerm, "and")){
			                $companies = $companies->where(function ($query) use ($searchTerm){
			                	$query->where('name', 'LIKE', "%$searchTerm%")
			                    ->orWhere('twitterPitch', 'LIKE', "%$searchTerm%")
			                    ->orWhere('city', 'LIKE', "%$searchTerm%")
			                    ->orWhere('url', 'LIKE', "%$searchTerm%")
			                    ->orWhere('visionAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('needsAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('teamAnswer', 'LIKE', "%$searchTerm%")
			                    ->orWhere('employees', '=', $searchTerm)
			                    ->orWhere('twitterHandle', 'LIKE', "%$searchTerm%");
			                });
			            }
		            }
		        }
		        $companies = $companies->orderBy($sort, $order)->groupBy('companies.id')->having('isPublished', '=', false)->paginate($limit);
		        $pills  = array();
		            array_push($pills, new Pill("Name", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'name', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'name', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("City", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'city', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'city', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-alpha-desc")
		                )));
		            array_push($pills, new Pill("Employees", array(
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'employees', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-numeric-asc"),
		                    new DropdownItem("", URL::route( 'archive', array('sort' => 'employees', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "sort-numeric-desc")
		                )));
		            array_push($pills, new Pill("Founded", array(
		                    new DropdownItem("Oldest first", URL::route( 'archive', array('sort' => 'yearFounded', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type)), ""),
		                    new DropdownItem("Youngest first", URL::route( 'archive', array('sort' => 'yearFounded', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)), "")
		                )));
		            array_push($pills, new Pill("Date Added", array(
		                    new DropdownItem("Oldest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit, 'type' => $type))),
		                    new DropdownItem("Newest first", URL::route( 'archive', array('sort' => 'created_at', 'order' => 'desc', 'search' => $search, 'limit' => $limit, 'type' => $type)))
		                )));
		        return View::make('archives.companies', array('total' => Company::Where('isPublished', '=', false)->count(), 'archivedCompanies' => $companies, 'sort' => $sort, 'order' => $order, 'search' => $search, 'limit' => $limit, 'pills' => $pills, 'type' => $type, 'archive' => true));
			}
		}
		return View::make('archives.index');
	}

}
