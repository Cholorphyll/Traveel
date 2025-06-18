@if (isset($hotels['data_status']) && $hotels['data_status'] == 4)   
    <p style="margin-left: 63px;
    color: red;"> Search is not finished, please try again.</p>
@elseif (!empty($hotels['result']))  
    @php
        $lowesttotals = [];
        $othertotals = [];

        foreach ($hotels['result'] as $hotel) {
            foreach ($hotel['rooms'] as $room) {
                $agencyName = $room['agencyName'];
                $total = $room['total'];
                $agencyId = $room['agencyId'];

                // Check if agencyName is already in the array or if the total is lower
                if (!isset($lowesttotals[$agencyName]) || $total < $lowesttotals[$agencyName]['total']) {
                    $lowesttotals[$agencyName] = ['total' => $total, 'fullBookingURL' => $room['fullBookingURL'], 'agencyId' => $agencyId];
                }

                // Store other totals
                $othertotals[$agencyName][] = ['total' => $total, 'fullBookingURL' => $room['fullBookingURL'], 'agencyId' => $agencyId];
            }
        }
    @endphp

    <?php 
    $latval = null; 
    $count = 0;
    ?>

    @foreach ($lowesttotals as $agencyName => $data)
        <div class="tr-travel-site">
            <div class="tr-travel-img">
           @if($count == 0)
            <div style="color: green;">cheapest deal</div>
            @endif
                <img loading="lazy" src="{{ 'http://pics.avs.io/hl_gates/100/30/' . $data['agencyId'] . '.png' }}" alt="Booking">
            </div>
            <div class="tr-travel-price">
                <a href="{{ $data['fullBookingURL'] }}" target="_blank"><strong>${{ $data['total'] }} </strong> /night</a>
            </div>
        </div>
        <?php 
        $latval = $data['total']; 
        $count++;
        ?>
    @endforeach

    {{-- This section is only shown when the data is not empty --}}
    <div style="text-align: center;">
        <?php
        // print_r($lowesttotals);
        // die();
        $countlowesttotals = count($lowesttotals);
        $countprice = count($othertotals);
        $counttotalrecord1 =count($othertotals) + count($lowesttotals) - 1;
        $lessprice ="";
        ?>
               <?php $a = 0; ?>
        @foreach ($othertotals as $agencyName => $totals)
            @foreach ($totals as $totalData)
                @php
                    $dataAlreadyDisplayed = false; // Initialize a flag to track if data has been displayed
                @endphp
                @foreach ($lowesttotals as $lowestAgencyName => $lowestData)
                    @if ($agencyName === $lowestAgencyName && $totalData['total'] === $lowestData['total'])
                        @php
                            $dataAlreadyDisplayed = true;
                            break;
                        @endphp
                    @endif
                @endforeach

         
                @if (!$dataAlreadyDisplayed)
                    <?php                
                   
                    if($a == 0){
                       
                        $lessprice = $totalData['total'];
                    }
                    $a++;
                    $count++;
                    ?>
                    <div class="tr-travel-site @if($count > 3) d-none @endif" style="border-top: 1px solid #E9E9E9;">
                        <div class="tr-travel-img">
                          
                            <img  src="{{ 'http://pics.avs.io/hl_gates/100/30/' . $totalData['agencyId'] . '.png' }}" alt="Booking">
                        </div>
                        <div class="tr-travel-price">
                            <a href="{{ $totalData['fullBookingURL'] }}" target="_blank"><strong>${{ $totalData['total'] }} </strong> /night</a>
                        </div>
                    </div>
                @endif
            @endforeach
        @endforeach
    </div>

    @if($counttotalrecord1 > 3)
        <?php $counttotalrecord1 -= 3; ?>
        <div class="tr-show-all">
            <button id="showMoreDeal" type="button" value="More">{{$counttotalrecord1}} more deals from ${{$latval}}</button>
            <button id="showLessDeal" type="button" value="Less" class="d-none">Show less deals from ${{$lessprice}}</button>
        </div>
    @endif
@else
    <div class="tr-sold-out">
        <img src="{{ asset('/frontend/hotel-detail/images/sold-out.png') }}" alt="Sold Out">
        <p>Please try different dates</p>
    </div>
@endif

<script>
    document.getElementById('showMoreDeal').addEventListener('click', function() {
        document.querySelectorAll('.tr-travel-site.d-none').forEach(function(el) {
            el.classList.remove('d-none');
        });
        document.getElementById('showMoreDeal').classList.add('d-none');
        document.getElementById('showLessDeal').classList.remove('d-none');
    });

    document.getElementById('showLessDeal').addEventListener('click', function() {
        let count = 0;
        document.querySelectorAll('.tr-travel-site').forEach(function(el) {
            count++;
            if (count > 3) {
                el.classList.add('d-none');
            }
        });
        document.getElementById('showMoreDeal').classList.remove('d-none');
        document.getElementById('showLessDeal').classList.add('d-none');
    });
</script>
