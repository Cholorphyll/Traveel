<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Upload Url</title>
  </head>
  <body>
  <div class="container-sm" style="max-width: 738px;">

    <h5 class="m-3" style="text-align: center;">Add Url</h5>
    <hr>
   
<form method="post" action="{{ route('form.process') }}">
    @csrf
    <div class="mb-3">
    <h6 class="m-3" style="float: right;"><a href="{{route('urlindex')}}">back</a></h6>
        <label >Upload Urls</label>
<br>
        <textarea name="urls" id="urls" cols="90" rows="25" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary" style="margin-left: 293px;">Submit</button>
</form>

  </div>

 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

 
  </body>
</html>