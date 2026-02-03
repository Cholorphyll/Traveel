@if(!$gettiming->isEmpty())
 @if($gettiming[0]->main_hours == 1)
			 			<span>Open with main hours</span>
					 @elseif($gettiming[0]->main_hours == 0)
					  <h6>Open with no main hours</h6>
					@endif
<table>

<?php
                      $data = json_decode($gettiming[0]->timings, true);
                      $daysOfWeek = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                      date_default_timezone_set('Asia/Kolkata'); // Set to Indian Standard Time

                      $currentDay = strtolower(date('D'));
                      $currentTime = date('H:i');

                      ?>

@foreach($daysOfWeek as $day)
<tr>
  <td>{{ ucfirst($day) }}</td>
  @if(isset($data['time'][$day]))
  <?php
                                  $startTime = $data['time'][$day]['start'];
                                  $endTime = $data['time'][$day]['end'];
                                  ?>

  @if($day === $currentDay)

  @if ($currentTime >= $startTime && $currentTime <= $endTime) @if($startTime=="00:00" && $endTime=="23:59" ) <td>| Open
    24 hours</td>
    @elseif($startTime == "00:00" && $endTime == "00:00")
    <td>| closed</td>
    @else
    <td>| {{ $startTime }} - {{ $endTime }}</td>
    @endif
    @else
    @if($startTime == "00:00" && $endTime == "23:59")
    <td>| Open 24 hours</td>
    @elseif($startTime == "00:00" && $endTime == "00:00")
    <td>| closed</td>
    @else
    <td>| {{ $startTime }} - {{ $endTime }}</td>
    @endif

    <!--    |  <span class="clsennow">Closed now</span> -->
    </td>
    @endif
    @else
    @if($startTime == "00:00" && $endTime == "23:59")
    <td>| Open 24 hours</td>
    @elseif($startTime == "00:00" && $endTime == "00:00")
    <td>| closed</td>
    @else
    <td>| {{ $startTime }} - {{ $endTime }}</td>
    @endif

    @endif
    @else
    <td>| <span class="">Not available</span></td>
    @endif
</tr>

@endforeach
</table>
@endif