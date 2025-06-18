<div class="tr-where-stay">
    <h3>Where to stay in {{$hname}}</h3>
    <div class="tr-stay-locations">

        <?php $a = 0; ?>
        @if(!empty($city_hotel_count))
            <div class="tr-stay-location">
                <h4>Cities nearby</h4>
                @foreach($city_hotel_count as $val)
                    <?php $a++; ?>
                    <ul>
                        <li><a href="{{$val['slug']}}">{{$val['city_name']}}</a></li>
                        <li><a href="{{$val['slug']}}">{{$val['hotel_count']}} hotels</a></li>
                    </ul>
                    @if($a % 5 == 0 && $a != count($city_hotel_count))
                        </div><div class="tr-stay-location"><h4>Cities nearby</h4>
                    @endif
                @endforeach
            </div>
        @endif

        @if(!empty($sight_hotel_count_grouped))
            <?php $b = 0; ?>
            @foreach($sight_hotel_count_grouped as $category => $sights)
                <?php $b++; ?>
                @if($b % 3 == 1)
                    <div class="tr-stay-location">
                @endif
                <h4>{{ $category }}</h4>
                @foreach($sights as $sightval)
                    <ul>
                        <li><a href="{{$sightval['slug']}}">{{ $sightval['Title'] }}</a></li>
                        <li><a href="{{$sightval['slug']}}">{{ $sightval['hotelcount'] }} hotels</a></li>
                    </ul>
                @endforeach
                @if($b % 3 == 0 || $b == count($sight_hotel_count_grouped))
                    </div>
                @endif
            @endforeach
        @endif

    </div>
</div>
