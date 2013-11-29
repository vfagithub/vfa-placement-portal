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
        $newPlacementStatus = new PlacementStatus();
            $newPlacementStatus->fellow_id = Input::get('fellow_id');
            $newPlacementStatus->opportunity_id = Input::get('opportunity_id');
            $newPlacementStatus->status = Input::get('status');
            if(Input::has('eventDate')){
                $newPlacementStatus->eventDate = Input::get('eventDate');
            }
            $newPlacementStatus->score = Input::get('score');
            $newPlacementStatus->message = Input::get('message');
            $newPlacementStatus->isRecent = 1;
        $oldPlacementStatuses = ::where('fellow_id','=',$newPlacementStatus->fellow_id)
                    ->where('opportunity_id','=',$newPlacementStatus->opportunity_id)
                    ->where('status', '<>',$newPlacementStatus->status)
                    ->get();
        $recentPlacementStatus = ::where('fellow_id','=',$newPlacementStatus->fellow_id)
                    ->where('opportunity_id','=',$newPlacementStatus->opportunity_id)
                    ->where('isRecent', '=',1);
                    ->first();
        if(array_search($newPlacementStatus->status, PlacementStatus::statuses()) > array_search($recentPlacementStatus->status, PlacementStatus::statuses())){
            try {
                $newPlacementStatus->save();
                //make sure only this new placement status is recent
                $oldPlacementStatuses->each(function($oldPlacementStatus)
                        {
                            $oldPlacementStatus->isRecent = 0;
                            $oldPlacementStatus->save();
                        });
                return Redirect::to(URL::back())->with('flash_notice', 'Relationship successfully updated.');
            } catch (ValidationFailedException $e) {
                return Redirect::to(URL::back())->with('flash_errors', $e->getErrorMessages());
            }
        } else {
            return Redirect::to(URL::back())->with('flash_error', 'Placement progress can\'t go backwards!');
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
