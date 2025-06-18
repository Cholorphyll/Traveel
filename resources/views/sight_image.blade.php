   @if(!$Sight_image->isEmpty())
                <div class="b-10 border border-3 border-c mb-4 bg-e text-center py-5">               
                   <img src="{{ asset('sight-images/'. $Sight_image[0]->Image) }}" alt="" />
                </div>
              @else
                <div class="b-10 border border-3 border-c mb-4 bg-e text-center py-5 addph">
                        <img src="{{ asset('/images/drop.png')}}" alt="drop" />
                        <p class="mb-0">Enhance this page</p>
                        <a href="#" class="text-dark" >Upload photos</a>
                    </div>
              @endif       