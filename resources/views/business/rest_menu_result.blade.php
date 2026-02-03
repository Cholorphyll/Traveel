 @if(!$getbusiness->isEmpty())
                @if($getbusiness[0]->Menu !="")
                  <?php $Menu = json_decode($getbusiness[0]->Menu, true); ?>
                    <ol>
                        @foreach($Menu as $item)
                            <li>{{ $item['menuitem'] }}</li>
                        @endforeach
                    </ol>
                @else
                  <p>Menu not found.</p>
                @endif
                  
              @else
                  <p>Menu not found.</p>
              @endif