<?php

class PitchInvitesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('pitchinvites.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('pitchinvites.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $newPitchInvite = new PitchInvite();
        $newPitchInvite->fellow_id = Input::get('fellow_id');
        $newPitchInvite->opportunity_id = Input::get('opportunity');
        $newPitchInvite->pitch_id = null;
        if ($newPitchInvite->opportunity_id != 0) {
        	try {
        	    $newPitchInvite->save();
        	    return Redirect::back()->with('flash_success', 'Pitch invite sent!');
        	} catch (ValidationFailedException $e) {
        	    return Redirect::back()->with('validation_errors', $e->getErrorMessages());
        	}
        } else {
        	return Redirect::back()->with('flash_error', "You must select an Opportunity to invite this fellow to pitch!");
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
        return View::make('pitchinvites.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('pitchinvites.edit');
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
