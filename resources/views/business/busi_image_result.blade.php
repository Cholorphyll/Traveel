<img
                src="@if($getdata[0]->user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$getdata[0]->user_image}} @else {{ asset('images/User_image.jpg')  }} @endif"
                alt="Profile Picture" class="img-fluid rounded-circle" style="width: 126px;
          height: 126px;
          border-radius: 100%;" data-bs-toggle="modal" data-bs-target="#exampleModalss"
                onclick="event.preventDefault()">
              <br>
              <a href="#" id="changePictureLink">Change Profile Picture</a>
              <form id="changeimg" action="{{route('busi_changeprofileimg')}}" method="post" enctype="multipart/form-data"
                style="display: none;">
                @csrf
                <input type="file" id="fileInput" name="pimage" style="display: none;">
                
              
                <input type="hidden" class="userid" value="{{$getdata[0]->id}}" name="userid">
                <input type="submit" value="Upload" id="uploadButton" style="width: 182px;display: none;"
                  class="form-control btn btn-primary d-none">
                <!-- <button style="display: none;" class=" form-control btn btn-danger">cancel</button> -->
              </form>