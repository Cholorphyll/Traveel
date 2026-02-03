<div class="tr-city-wise-hotel-listing">      
    @foreach ($citiesWithHotels->chunk(5) as $cityChunk)
    <div class="tr-city-wise-hotel-lists">
        @foreach ($cityChunk as $city)
        <div class="tr-city-wise-hotel-list">
            <div class="tr-city-name">
                <a href="{{ url('ho-'.$city->slugid.'-'.strtolower(str_replace(' ', '_', str_replace('#', '!', $city->Slug)))) }}" style="color: black;">{{ $city->Name }} hotels</a>
            </div>
            <div class="tr-hotel-lists">
                @foreach ($city->hotels as $hotel)
                <div class="tr-hotel-list">
                    <div class="tr-hotel-name">
                        <a href="{{ url('hd-'.$city->slugid.'-' .$hotel->id .'-'.strtolower(str_replace(' ', '_', str_replace('#', '!', $hotel->slug)))) }}">
                            {{ $hotel->name }}
                        </a>
                    </div>
                    <div class="tr-hotel-price">
                        @if($hotel->pricefrom != "") ${{ $hotel->pricefrom }} @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
    @endforeach
</div>