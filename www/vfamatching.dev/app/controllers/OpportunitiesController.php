<?php

class OpportunitiesController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('adminOrHiringManager', array('only' => array('publish','unpublish')));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        // $opportunities = Opportunity::all();
        $sort = (!is_null(Input::get('sort')) ? Input::get('sort') : 'title'); //default to opportunity title
        $order = (!is_null(Input::get('order')) ? Input::get('order') : 'asc'); //default to asc
        $search = (!is_null(Input::get('search')) ? Input::get('search') : ''); //default to empty string
        $limit = (!is_null(Input::get('limit')) ? Input::get('limit') : 20); //default to 5
        $opportunities = Opportunity::select('*', 'companies.name', 'companies.id', 'opportunities.city', 'opportunities.id', 'opportunities.created_at', 'opportunities.updated_at')
            ->join('companies', 'opportunities.company_id', '=', 'companies.id')
            ->leftJoin('opportunityTags', 'opportunities.id', '=', 'opportunityTags.opportunity_id');
        if($search != ''){
            $searchTerms = explode(' ', $search);
            //requiring a match on every keyword from any field
            foreach($searchTerms as $searchTerm){
                if(strcasecmp($searchTerm, "and")){
                    //using advanced wheres from http://stackoverflow.com/questions/16995102/laravel-4-eloquent-where-with-and-or
                    $opportunities = $opportunities->where(function ($query) use ($searchTerm) {
                        $query->where('title', 'LIKE', "%$searchTerm%")
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
        if(Auth::user()->role == "Hiring Manager"){//Ensure Hiring Manaers only see their Opportunities
            $opportunities = $opportunities->where('companies.id','=',Auth::user()->profile->company->id);
            $total = Opportunity::join('companies', 'opportunities.company_id', '=', 'companies.id')
            ->where('opportunities.isPublished', '=', true)->where('companies.id','=',Auth::user()->profile->company->id)->count();
        } else {
            $total = Opportunity::Where('opportunities.isPublished', '=', true)->count();
        }
        $opportunities = $opportunities->orderBy($sort, $order)->groupBy('opportunities.id')->having('opportunities.isPublished', '=', true)->paginate($limit);
        $pills  = array();
            array_push($pills, new Pill("Title", array(
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'opportunities.title', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'opportunities.title', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Company", array(
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'companies.name', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'companies.name', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("City", array(
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'opportunities.city', 'order' => 'asc', 'search' => $search, 'limit' => $limit)), "sort-alpha-asc"),
                    new DropdownItem("", URL::route( 'opportunities.index', array('sort' => 'opportunities.city', 'order' => 'desc', 'search' => $search, 'limit' => $limit)), "sort-alpha-desc")
                )));
            array_push($pills, new Pill("Date Added", array(
                    new DropdownItem("Oldest first", URL::route( 'opportunities.index', array('sort' => 'opportunities.created_at', 'order' => 'asc', 'search' => $search, 'limit' => $limit))),
                    new DropdownItem("Newest first", URL::route( 'opportunities.index', array('sort' => 'opportunities.created_at', 'order' => 'desc', 'search' => $search, 'limit' => $limit)))
                )));
        return View::make('opportunities.index', array('total' => $total, 'opportunities' => $opportunities, 'sort' => $sort, 'order' => $order, 'search' => $search, 'limit' => $limit, 'pills' => $pills));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return View::make('opportunities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $newOpportunity = new Opportunity();
        $newOpportunity->company_id = Input::get('company_id');
        $newOpportunity->isPublished = 1;
        $newOpportunity->title = Input::get('title');
        $newOpportunity->teaser = Input::get('teaser');
        $newOpportunity->city = Input::get('city');
        $newOpportunity->description = Input::get('description');
        $newOpportunity->responsibilitiesAnswer = Input::get('responsibilitiesAnswer');
        $newOpportunity->skillsAnswer = Input::get('skillsAnswer');
        $newOpportunity->developmentAnswer = Input::get('developmentAnswer');

        // *** Commented out in lieu of Job Types ***
        // $opportunityTags = array();
        // if (Input::has('tags')){
        //     //TODO: Validate that this is a pdf
        //     $tags = explode(',', Input::get('tags'));
        //     //trim each skill
        //     array_walk($tags, function(&$value, $key){
        //         $value = trim($value);
        //     });
        //     foreach($tags as $tag){
        //         $opportunityTag = new OpportunityTag();
        //         $opportunityTag->tag = $tag;
        //         array_push($opportunityTags, $opportunityTag);
        //     }
        // }

        $opportunityTags = array();
        if (Input::has('jobType')){
            foreach(Input::get('jobType') as $jobType){
                $opportunityTag = new OpportunityTag();
                $opportunityTag->tag = $jobType;
                array_push($opportunityTags, $opportunityTag);
            }
        }

        try {
            $newOpportunity->save();
            foreach($opportunityTags as $opportunityTag){
                $opportunityTag->opportunity_id = $newOpportunity->id;
                $opportunityTag->save();
            }
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages())->withInput();
        }

        return Redirect::route('opportunities.index')->with('flash_success', 'Opportunity successfully created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        try{
            $opportunity = Opportunity::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Opportunity not found!');
        }
        if(Auth::user()->role == "Hiring Manager"){
            if($opportunity->company->id != Auth::user()->profile->company->id){
                return Redirect::route('dashboard')->with('flash_error', "You don't have the necessary permissions to do that!");
            }
        }
        return View::make('opportunities.show')->with('opportunity',$opportunity);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return View::make('opportunities.edit');
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

    public function publish($id)
    {
        try{
            $opportunity = Opportunity::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Opportunity not found!');
        }
        $opportunity->isPublished = true;
        $opportunity->save();
        return Redirect::back()->with('flash_success', "Opportunity published");
    }

    public function unpublish($id)
    {
        try{
            $opportunity = Opportunity::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Opportunity not found!');
        }
        //TODO: Cascade the effects of unpublishing an opportunity
        $opportunity->isPublished = false;
        $opportunity->save();
        return Redirect::back()->with('flash_success', "Opportunity unpublished");
    }

}
