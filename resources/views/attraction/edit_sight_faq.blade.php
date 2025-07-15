<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Edit Faq</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">EDIT FAQ</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <div class="row justify-content-center">
                <div class="col-md-12">
                  <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                      <div class="heading1 margin_0">
                      </div>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="col-md-8 alert alert-success mt-3">
                      {{ $message }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="col-md-8 alert alert-danger mt 3">
                      <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                    @endif
                    @if ($message = Session::get('error'))
                    <div class="col-md-8 alert alert-danger mt-3">
                      {{ $message }}
                    </div>
                    @endif
                    <br>
                    <!-- Button trigger modal -->
                    <span type="button" class="float-right" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                      style="float:right">
                      <h4><u>ADD FAQ</u></h4>
                    </span>
                    <span class="getupdateddata">
                    @if(!$getfaq->isEmpty())

                    <!-- <form class="" action="{{route('update_faq')}}" method="POST"> -->
                    @csrf

                    <div class="row">
                      <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group mt-3">
                          <span id="Success"></span>
                          <strong>Attraction</strong>
                          <div class="form-search form-search-icon-right">
                            <input type="text" id="search_attractionfaq" name="sightname"
                              value="{{ $getfaq[0]->Title }}" class="search_attractionfaqs form-control rounded-3"
                              placeholder="Search Attraction" disabled>
                          </div>
                          <input type="hidden" name='attrid' id="selected_att_id" value="{{ $getfaq[0]->SightId }}"
                            class="form-control rounded-3" required>

                          @foreach($getfaq as $faqItem)

                          <div class="form-group getdata-{{ $faqItem->Id }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" value="0"
                              onclick="editsightfaq({{ $faqItem->Id }})">Edit</span>
                            <strong>Faquestion</strong>
                            <div id="faquestionmsg-{{ $faqItem->Id }}"></div>
                            <textarea type="text" name="faquestion" data-original-value="{{ $faqItem->Faquestion }}"
                              id="faquestion{{ $faqItem->Id }}" class="form-control rounded-3"
                              disabled>{{ $faqItem->Faquestion }}</textarea>
                          </div>

                          <div class="form-group">
                            <strong>Answer</strong>
                            <textarea type="text" name="answer" data-original-value="{{ $faqItem->Answer }}"
                              id="answer{{ $faqItem->Id }}" class="form-control rounded-3" placeholder="Enter answer here"
                              disabled>{{ $faqItem->Answer }}</textarea>
                            <input type="hidden" id="faqId{{ $faqItem->Id }}" value="{{ $faqItem->Id }}">
                            <input type="hidden" id="sightId{{ $faqItem->Id }}" value="{{ $faqItem->SightId }}">
                          </div>

                          <span id="buttonsContainer-{{ $faqItem->Id }}"
                            class="buttons-container-dd d-none mb-3 " data-colid="{{ $faqItem->Id }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 updatesightfaq"
                              data-id="{{ $faqItem->Id }}">Save</button>
                            <button type="button" value="2"
                              onclick="cancelbtn({{ $faqItem->Id }})"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>
                          @endforeach
                        </div>
                      </div>
                    </div>
                    </form>
                    @else
                    <p>FAQ Not Found.</p>
                    @endif

                    </span>
                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                      tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content" style="width: 647px;padding: 20px;">
                          <div class="modal-header">
                            <!-- <h3 class="modal-title" id="staticBackdropLabel"> -->
                            <span class="list-faq" style="font-weight: bold; text-decoration: underline;">
                              Add from list</span>
                            <span class="custom-faq margin-l">Add
                              Custom</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <h4 class="text-center">Add New FAQ</h4>

                            <div class="container">
                              <p class="mt-3">Add From List</p>
                              <p id="errorcheck"></p>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" value="" id="checkbox1">
                                <span>What are some of the best places to go out to eat near the Attraction?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox2">
                                <span>Are there any waterfalls within a short drive of the attraction?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox3">
                                <span>What are some of the nearest parks to the attraction?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox4">
                                <span>What are some of the best places to go hiking or biking near the attraction?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox4">
                                <span>What are some of the best places to go shopping near the attraction?</span>
                              </label>
                              <br>
                              <br>
                              <span class="mt-3  list-buttons">
                                <button id="save_att_faq" data-id="{{request()->route('id') }}"
                                  class="btn btn-dark">Save</button>
                                <button id="cancel-list" type="button" class="btn btn-dark margin-l"
                                  data-bs-dismiss="modal">Cancel</button>
                              </span>

                              <hr>
                              <p id="errorcustfaq"></p>
                              <label for="">Faquestion</label>
                              <textarea type="text" name="Faquestion" id="addques"
                                class="form-control rounded-3"></textarea>
                               <label for="">Answer</label>
                              <textarea type="text" name="answer" 
                              data-id="" id="addans" class="form-control rounded-3"
                              ></textarea> 
                              <br>
                              <br>
                              <span class="mt-3  custom-faq-buttons">
                                <button id="saveAtt_cusfaq" data-id="{{request()->route('id')}}" class="btn btn-dark"
                                  disabled>Save</button>
                                <button id="cancel-list" type="button" class="btn btn-dark margin-l"
                                  data-bs-dismiss="modal" disabled>Cancel</button>
                              </span>

                            </div>

                            <div class="modal-footer mt-3">
                              <!-- <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button> -->

                            </div>
                          </div>
                        </div>
                      </div>





                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

@push('scripts')
<script>
  // Function to enable editing of FAQ
  function editsightfaq(id) {
    // Enable the form fields for editing
    $('#faquestion' + id).prop('disabled', false);
    $('#answer' + id).prop('disabled', false);
    
    // Show the buttons container
    $('#buttonsContainer-' + id).removeClass('d-none');
  }
  
  // Function to cancel editing
  function cancelbtn(id) {
    // Disable the form fields
    $('#faquestion' + id).prop('disabled', true);
    $('#answer' + id).prop('disabled', true);
    
    // Hide the buttons container
    $('#buttonsContainer-' + id).addClass('d-none');
    
    // Restore original values
    $('#faquestion' + id).val($('#faquestion' + id).attr('data-original-value'));
    $('#answer' + id).val($('#answer' + id).attr('data-original-value'));
  }
  
  // Handle the save button click for updating FAQ
  $(document).on('click', '.updatesightfaq', function() {
    var faqId = $(this).data('id'); // This is now the actual FAQ ID
    var sightId = $('#sightId' + faqId).val(); // Get the sight ID from the hidden field
    var faquestion = $('#faquestion' + faqId).val();
    var answer = $('#answer' + faqId).val() || ''; // Ensure answer is at least an empty string
    
    console.log('Updating FAQ with ID:', faqId);
    console.log('SightId:', sightId);
    console.log('Question:', faquestion);
    console.log('Answer:', answer);
    console.log('Answer element exists:', $('#answer' + faqId).length > 0);
    
    // Perform AJAX request to update the FAQ
    $.ajax({
      url: '{{ route("update_faq") }}',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        faqId: faqId,
        faquestion: faquestion,
        answer: answer
      },
      success: function(response) {
        if (response.success) {
          // Update the original values - using faqId instead of sightId
          $('#faquestion' + faqId).attr('data-original-value', faquestion);
          $('#answer' + faqId).attr('data-original-value', answer);
          
          // Show success message
          $('#Success').html('<div class="alert alert-success">' + response.message + '</div>');
          
          // Reset the form - using faqId instead of sightId
          cancelbtn(faqId);
          
          // Hide success message after 3 seconds
          setTimeout(function() {
            $('#Success').html('');
          }, 3000);
        } else {
          // Show error message
          $('#Success').html('<div class="alert alert-danger">' + (response.message || 'Failed to update FAQ!') + '</div>');
        }
      },
      error: function(xhr, status, error) {
        // Check if there are validation errors
        if (xhr.responseJSON && xhr.responseJSON.errors) {
          var errorMessages = '';
          $.each(xhr.responseJSON.errors, function(key, value) {
            errorMessages += value[0] + '<br>';
          });
          $('#Success').html('<div class="alert alert-danger">' + errorMessages + '</div>');
        } else {
          // Show general error message
          $('#Success').html('<div class="alert alert-danger">An error occurred while updating FAQ!</div>');
        }
      }
    });
  });
</script>
@endpush

</x-app-layout>