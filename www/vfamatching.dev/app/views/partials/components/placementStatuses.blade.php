{{-- Requires $placementStatuses --}}
@if(count($placementStatuses))
<div class="placementStatuses">
    <h2><small>{{ $heading }}</small></h2>
    <div class="row">
    <?php $count = 0; ?>
    @foreach($placementStatuses as $placementStatus)
        @include('partials.indexes.placementStatus', array('placementStatus' => $placementStatus))
        <?php 
            $count++;
            if($count % 3 == 0){
                echo '<div class="row"></div>';
            }
        ?>
    @endforeach
    </div>
</div>
@endif