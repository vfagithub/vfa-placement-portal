<?php

class PlacementStatusesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('placementstatuses.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('placementstatuses.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(Input::get('score') == ""){
			//Edge case that Laravel model validation doesn't catch
			return Redirect::back()->with('flash_error', 'You must select a feedback score to update your placement progress!');
		}

        $oldPlacementStatuses = PlacementStatus::where('fellow_id','=',Input::get('fellow_id'))
                    ->where('opportunity_id','=',Input::get('opportunity_id'))
                    ->get();
        $newPlacementStatus = new PlacementStatus();
            $newPlacementStatus->fellow_id = Input::get('fellow_id');
            $newPlacementStatus->opportunity_id = Input::get('opportunity_id');
            $newPlacementStatus->status = Input::get('status');
            if(Input::has('eventDate')){
                $newPlacementStatus->eventDate = Carbon::createFromFormat('m-d-Y', Input::get('eventDate'))->toDateTimeString();
            }
            $newPlacementStatus->score = Input::get('score');
            $newPlacementStatus->message = Input::get('message');
            $newPlacementStatus->isRecent = 1;
            $newPlacementStatus->fromRole = Auth::user()->role;
        $recentPlacementStatus = PlacementStatus::where('fellow_id','=',$newPlacementStatus->fellow_id)
                    ->where('opportunity_id','=',$newPlacementStatus->opportunity_id)
                    ->where('isRecent', '=',1)
                    ->first();
        if(array_search($newPlacementStatus->status, PlacementStatus::statuses()) >= array_search($recentPlacementStatus->status, PlacementStatus::statuses())){
            try {
                $newPlacementStatus->save();
                if($newPlacementStatus->status == "Offer Accepted"){
                	//Send fellow notification email
                	$mailer = new Mailers\FellowMailer();
                	$mailer->fellowAcceptedOffer($newPlacementStatus);
                	//TODO: Email hiring managers
                	//TODO: Email other Fellows
                	//TODO: Unpublish fellow
                	$fellow = $newPlacementStatus->fellow;
                	$fellow->isPublished = 0;
                	$fellow->save();
                	//TOOD: Unpublish Opportunity
                	$opportunity = $newPlacementStatus->opportunity;
                	$opportunity->isPublished = 0;
                	$opportunity->save();
                	//TODO: Set other Placement Statuses for this Opportunity to isRecent = false
                	foreach($opportunity->placementStatuses as $placementStatus){
                		if($placementStatus->id != $newPlacementStatus->id){
                			$placementStatus->isRecent = false;
                			$placementStatus->save();
                		}
                	}
                }
                //make sure only this new placement status is recent
                $oldPlacementStatuses->each(function($oldPlacementStatus)
                        {
                            $oldPlacementStatus->isRecent = 0;
                            $oldPlacementStatus->save();
                        });
                return Redirect::back()->with('flash_success', 'Placement Status successfully updated.');
            } catch (ValidationFailedException $e) {
                return Redirect::back()->with('validation_errors', $e->getErrorMessages());
            }
        } else {
            return Redirect::back()->with('flash_error', 'You must select a new status to update your placement progress!');
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('placementstatuses.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('placementstatuses.edit');
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

}
