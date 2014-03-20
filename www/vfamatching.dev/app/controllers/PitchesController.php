<?php

class PitchesController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('pitches.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('pitches.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$newPitch = new Pitch();
        $newPitch->fellow_id = Input::get('fellow_id');
        $newPitch->opportunity_id = Input::get('opportunity_id');
        $newPitch->status = "Under Review";
        $newPitch->body = Input::get('body');

        try {
            $newPitch->save();
            if(PitchInvite::hasPitchInvite(Fellow::find($newPitch->fellow_id), Opportunity::find($newPitch->opportunity_id))){
            	//update the pitchinvite
            	$oldPitchInvite = PitchInvite::where('fellow_id','=', $newPitch->fellow_id)->where('opportunity_id', '=', $newPitch->opportunity_id)->first();
            	$oldPitchInvite->pitch_id = $newPitch->id;
            	$oldPitchInvite->save();
            }
            //email admins about new pitch
            $mailer = new Mailers\AdminMailer();
            $mailer->newFellowPitch($newPitch);
            //make sure only this new placement status is recent
            return Redirect::back()->with('flash_success', 'Pitch successfully submitted.');
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages());
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
        return View::make('pitches.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('pitches.edit');
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
		if(Auth::user()->role == "Admin"){
            try{
                $pitch = Pitch::findOrFail($id);
                $pitch->delete();
            } catch (ValidationFailedException $e) {
                return Redirect::back()->with('flash_error', "There was an error deleting the pitch!");
            }
            return Redirect::back()->with('flash_success', "Pitch waitlisted");
        } else {
            return Redirect::back()->with('flash_error', "Only Admins can delete Pitches...");
        }
	}

	public function approve($id)
    {
        try{
            $pitch = Pitch::findOrFail($id);
            if(Auth::user()->role == "Hiring Manager"){
                if($pitch->hasAdminApproval){
                    $pitch->status = "Approved";
                    $pitch->save();
                    //email the Fellow to let them know
                    $mailer = new Mailers\FellowMailer();
                    $mailer->hiringManagerApprovedFellowPitch($pitch);
                    //introduce the two
                    $newPlacementStatus = new PlacementStatus();
                    $newPlacementStatus->fellow_id = $pitch->fellow_id;
                    $newPlacementStatus->opportunity_id = $pitch->opportunity_id;
                    $newPlacementStatus->fromRole = "Admin";
                    $newPlacementStatus->status = "Introduced";
                    $newPlacementStatus->eventDate = null;
                    $newPlacementStatus->score = null;
                    $newPlacementStatus->message = "";
                    $newPlacementStatus->isRecent = 1;
                    $newPlacementStatus->save();
                } else {
                    throw new Exception("Hiring Managers can't approve Pitches until they've been approved by an Admin!");
                }
            } elseif(Auth::user()->role == "Admin"){
                $pitch->hasAdminApproval = true;
                $pitch->status = "Under Review"; //in case the admin waitlisted it
                $pitch->save();
                //email Hiring Managers to let them know
                $mailer = new Mailers\HiringManagerMailer();
                $mailer->newFellowPitch($pitch);
                //email Fellow to let them know
                $mailer = new Mailers\FellowMailer();
                $mailer->adminApprovedFellowPitch($pitch);
            } else {
                throw new Exception("Only Admins and Hiring Managers can approve pitches!");
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Pitch not found!');
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages());
        }
        return Redirect::back()->with('flash_success', "Pitch approved!");
    }

    public function waitlist($id)
    {
        try{
            $pitch = Pitch::findOrFail($id);
	        $pitch->status = "Waitlisted";
	        $pitch->save();
            if(Auth::user()->role == "Hiring Manager"){
                //notify Fellow that thier pitch was waitlisted
                $mailer = new Mailers\FellowMailer();
                $mailer->hiringManagerWaitlistedFellowPitch($pitch);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return View::make('404')->with('error', 'Pitch not found!');
        } catch (ValidationFailedException $e) {
            return Redirect::back()->with('validation_errors', $e->getErrorMessages());
        }
        return Redirect::back()->with('flash_success', "Pitch waitlisted");
    }

}
