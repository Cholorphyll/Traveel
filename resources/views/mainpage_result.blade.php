@if (!empty($searchresults))
  @foreach ($searchresults as $searchresult)
    <li>
      <?php 
        // Check if this is a "Result not found" entry
        if(isset($searchresult['value']) && $searchresult['value'] == "Result not found") {
            $url = "#";
            $locationName = "Result not found";
            $details = "";
        } else {
                if($sight == 1 && isset($searchresult['id']) && isset($searchresult['SightId']) && isset($searchresult['Slug'])){
                  $url = url('at-'.$searchresult['id'].'-'.$searchresult['SightId'].'-'.strtolower(str_replace(' ', '_', $searchresult['Slug'])));
                } else if(isset($searchresult['id']) && isset($searchresult['Slug'])) {
                  $url = url('lo-'.$searchresult['id'].'-'.strtolower(str_replace(' ', '_', $searchresult['Slug'])));
                } else {
                  $url = "#";
                }
                
                // Split the value into location name and details
                if(isset($searchresult['value'])) {
                    // Check if there's a comma in the value
                    if(strpos($searchresult['value'], ',') !== false) {
                        $parts = explode(',', $searchresult['value'], 2); // Split only at the first comma
                        $locationName = trim($parts[0]);
                        $details = isset($parts[1]) ? trim($parts[1]) : '';
                        
                        // Remove any leading commas from details
                        $details = ltrim($details, ', ');
                    } else {
                        // No comma found, just use the whole value as the location name
                        $locationName = trim($searchresult['value']);
                        $details = '';
                    }
                } else {
                    $locationName = "Location not found";
                    $details = "";
                }
            }
          ?>
      <a class="custom-link" href="{{$url}}" style="display: block; text-decoration: none; color: inherit;">
        <div class="tr-place-info">
          <div class="tr-location-icon">
            <svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_441_2146)"><path d="M13.125 6.73828C13.125 11.1133 7.5 14.8633 7.5 14.8633C7.5 14.8633 1.875 11.1133 1.875 6.73828C1.875 5.24644 2.46763 3.8157 3.52252 2.76081C4.57742 1.70591 6.00816 1.11328 7.5 1.11328C8.99184 1.11328 10.4226 1.70591 11.4775 2.76081C12.5324 3.8157 13.125 5.24644 13.125 6.73828Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 8.61328C8.53553 8.61328 9.375 7.77382 9.375 6.73828C9.375 5.70275 8.53553 4.86328 7.5 4.86328C6.46447 4.86328 5.625 5.70275 5.625 6.73828C5.625 7.77382 6.46447 8.61328 7.5 8.61328Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_441_2146"><rect width="15" height="15" fill="white" transform="translate(0 0.488281)"/></clipPath></defs></svg>
          </div>
          <div class="tr-location-info">
            <div class="tr-hotel-name">{{ $locationName }}</div>
            <div class="tr-hotel-city">{{ $details }}</div>
          </div>
        </div>
      </a>
    </li>
  @endforeach
@else
@endif