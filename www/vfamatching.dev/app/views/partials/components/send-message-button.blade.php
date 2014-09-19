@if(Auth::user()->role == "Hiring Manager")
    <a data-toggle="modal" href="#message-modal-{{ $fellow->id }}" class="btn btn-success modal-btn form-control"><i class="fa fa-comment"></i> Message</a>
	<!-- Modal -->
	<div class="modal" id="message-modal-{{ $fellow->id }}">
	    <div class="modal-dialog">
	        <div class="modal-content">
	            <div class="modal-header">
	                <a href="#" class="btn close btn-default" data-dismiss="modal">&times;</a>
	                <h4 class="modal-title">Send a message to {{ $fellow->firstName . ' ' . $fellow->lastName }}</h4>
	            </div>
	            <div class="modal-body">
	                @if(Auth::user()->role == "Hiring Manager")
	                    @include('partials.forms.pitch', array('fellow_id' => Auth::user()->profile->id, 'opportunity_id' => $opportunity->id))
	                @endif
	            </div>
	            <div class="modal-footer">
	                <a href="" class="btn btn-default" data-dismiss="modal">Cancel</a>
	                <a href="" class="btn btn-primary pitch-submit">Submit</a>
	            </div>
	        </div><!-- /.modal-content -->
	    </div><!-- /.modal-dialog -->
	</div><!-- /.modal -->
@endif