<x-app-layout>
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Add Hotel</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h2 class="mb-0">Add Hotel</h2>
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

                                        @if ($errors->any())
                                            <div class="col-md-8 alert alert-danger mt 3">
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <br>

                                        <form class="" action="{{ route('storeHotel', ['id' => $hotel->id ?? null]) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <strong>Hotel Name</strong>
                                                            <input type="text" name="hotel_name" value=""
                                                                class="form-control rounded-3" placeholder="Hotel Name"
                                                                required>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <strong>Page Slug</strong>
                                                            <input type="text" name="slug"
                                                                class="form-control rounded-3" value=""
                                                                placeholder="Page slug" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Meta Title</strong>
                                                            <input type="text" name='MetaTagTitle'
                                                                class="form-control rounded-3" placeholder="">
                                                            <input type="hidden" value="" id="sightid">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Meta Description</strong>
                                                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3" placeholder=""> </textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>About Hotel</strong>
                                                                <textarea type="text" name='about' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Short Description</strong>
                                                                <textarea type="text" name='short_description' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Spotlight</strong>
                                                                <textarea type="text" name='Spotlights' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Things to Know</strong>
                                                                <textarea type="text" name='ThingstoKnow' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>checkIn</strong>
                                                                <textarea type="text" name='checkIn' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>checkOut</strong>
                                                                <textarea type="text" name='checkOut' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Highlights</strong>
                                                                <textarea type="text" name='Highlights' value="" class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Reviews</strong>
                                                                <br>

                                                                <div id="reviews-container">
                                                                    <div class="col-md-12 form-group mt-3">
                                                                        <strong>Review</strong>
                                                                        <textarea name="review[]" class="form-control rounded-3"></textarea>
                                                                        <input type="hidden" name="reviewId[]"
                                                                            value="">
                                                                        <strong>Rating (out of 5)</strong>
                                                                        <input type="number" name="rating[]"
                                                                            min="1" max="5"
                                                                            value="{{ $review->Rating ?? 5 }}"
                                                                            class="form-control rounded-3">
                                                                    </div>
                                                                    <hr>
                                                                </div>

                                                                <!-- Add Review Button to open the modal -->
                                                                <button type="button" class="btn btn-dark mt-3"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addReviewModal">
                                                                    Add Review
                                                                </button>

                                                                <!-- Add Review Modal -->
                                                                <div class="modal fade" id="addReviewModal"
                                                                    tabindex="-1"
                                                                    aria-labelledby="addReviewModalLabel"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="addReviewModalLabel">Add New
                                                                                    Review</h5>
                                                                                <button type="button"
                                                                                    class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group mt-3">
                                                                                    <strong>Review</strong>
                                                                                    <textarea id="newReviewContent" class="form-control rounded-3"></textarea>
                                                                                    <strong>Rating (out of 5)</strong>
                                                                                    <input id="newReviewRating"
                                                                                        type="number" min="1"
                                                                                        max="5"
                                                                                        class="form-control rounded-3">
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                <button type="button"
                                                                                    id="addReviewButton"
                                                                                    class="btn btn-primary">Add
                                                                                    Review</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <h3><u>FAQ Section</u></h3>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>FAQ</strong>
                                                                <br>

                                                                <div class="row" id="faq-container">
                                                                    <div class="col-md-12 form-group mt-3">
                                                                        <strong>Question</strong>
                                                                        <textarea name="question[]" class="form-control rounded-3"></textarea>
                                                                        <input type="hidden" name="faqId[]"
                                                                            value="">
                                                                    </div>
                                                                    <div class="col-md-12 form-group mt-3">
                                                                        <strong>Answer</strong>
                                                                        <textarea name="answer[]" class="form-control rounded-3"></textarea>
                                                                    </div>
                                                                    <hr>
                                                                </div>


                                                                <!-- Add FAQ Button to open a modal -->
                                                                <button type="button" class="btn btn-dark mt-3"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#addFaqModal">
                                                                    Add FAQ
                                                                </button>

                                                                <!-- Add FAQ Modal -->
                                                                <div class="modal fade" id="addFaqModal"
                                                                    tabindex="-1" aria-labelledby="addFaqModalLabel"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="addFaqModalLabel">Add New FAQ
                                                                                </h5>
                                                                                <button type="button"
                                                                                    class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <div class="form-group mt-3">
                                                                                    <strong>Question</strong>
                                                                                    <textarea id="newFaqQuestion" class="form-control rounded-3"></textarea>
                                                                                </div>
                                                                                <div class="form-group mt-3">
                                                                                    <strong>Answer</strong>
                                                                                    <textarea id="newFaqAnswer" class="form-control rounded-3"></textarea>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button"
                                                                                    class="btn btn-secondary"
                                                                                    data-bs-dismiss="modal">Close</button>
                                                                                <button type="button"
                                                                                    id="addFaqButton"
                                                                                    class="btn btn-primary">Add
                                                                                    FAQ</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>



                                                </div>

                                                <h3><u>Location Info</u></h3>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Address Line 1</strong>
                                                            <?php // $address = explode(',',$gethotel[0]->Address);
                                                            //  print_r($address);
                                                            ?>
                                                            <textarea type="text" name='addressline1' class="form-control rounded-3" placeholder=""></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Address Line 2</strong>
                                                            <input type="text" name='addressline2' value=""
                                                                class="form-control rounded-3"
                                                                placeholder="addressline2">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Neighborhood</strong>
                                                            <!-- <textarea type="text" name='neighborhood' value="" class="form-control rounded-3"></textarea> -->

                                                            <?php if(!$neighborhoodlist->isEmpty()){  ?>
                                                            <select class="form-select"
                                                                aria-label="Default select example"
                                                                name='neighborhood'>
                                                                <option value="" selected>Select Neighborhood
                                                                </option>
                                                                @foreach ($neighborhoodlist as $nlist)
                                                                    <option value="{{ $nlist->NeighborhoodId }}">
                                                                        {{ $nlist->Name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>City</strong>
                                                            <div class="form-search form-search-icon-right">
                                                                <input type="text" id="searchHotelcity"
                                                                    name="ctname" class="form-control rounded-3"
                                                                    placeholder=""><i class="ti ti-search"></i>
                                                            </div>

                                                            <span id="citylisth"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">


                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Country</strong>
                                                            <input type="text" id="country" class="form-control"
                                                                name="county" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Pincode</strong>
                                                            <input type="number" name='pincode'
                                                                class="form-control rounded-3" placeholder="Pincode">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Latitude</strong>
                                                            <input type="text" name='Latitude'
                                                                class="form-control rounded-3"
                                                                placeholder="Enter Latitude">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Longitude</strong>
                                                            <input type="text" name='Longitude'
                                                                class="form-control rounded-3"
                                                                placeholder="Enter Longitude">
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>stars</strong>
                                                                <input type="text" name='stars' value=""
                                                                    class="form-control rounded-3"
                                                                    placeholder="Enter stars">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Price</strong>
                                                                <input type="text" name='pricefrom' value=""
                                                                    class="form-control rounded-3"
                                                                    placeholder="Enter pricefrom">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Property Type</strong>
                                                                <select class="form-select"
                                                                    aria-label="Default select example"
                                                                    name="propertyType">
                                                                    <option value="" selected>Select Property
                                                                        Type</option>
                                                                    <option value="4">Apartment / Condominium
                                                                    </option>
                                                                    <option value="11">Lodge</option>
                                                                    <option value="9">Farm Stay</option>
                                                                    <option value="5">Motel</option>
                                                                    <option value="12">Villa</option>
                                                                    <option value="0">Other</option>
                                                                    <option value="1">Hotel</option>
                                                                    <option value="6">Guest House</option>
                                                                    <option value="13">Room</option>
                                                                    <option value="7">Hostel</option>
                                                                    <option value="2">Apartment Hotel</option>
                                                                    <option value="3">Bed & Breakfast</option>
                                                                    <option value="8">Resort</option>
                                                                    <option value="10">Vacation Rental</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Amenities</strong>
                                                                <textarea type="text" name='amenities' class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Short Facilities</strong>
                                                                <textarea type="text" name='shortFacilities' class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Language</strong>
                                                                <textarea type="text" name='Languages' class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Room Amenitiy</strong>
                                                                <textarea type="text" name='room_aminities' class="form-control rounded-3" placeholder=""></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Location Score</strong>
                                                                <input type="text" name='location_score'
                                                                    value="" class="form-control rounded-3"
                                                                    placeholder="Enter Location Score">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-12">
                        <div id = "map1" style = "width: 500px; height: 250px"></div>
                        </div> -->
                                                    <h3 class="mt-5"><u>Contact Info</u></h3>

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Website</strong>
                                                                <input type="text" name='website'
                                                                    class="form-control rounded-3"
                                                                    placeholder="Enter website url">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Phone</strong>
                                                                <input type="text" name="phone"
                                                                    value="{{ old('phone', $gethotel[0]->Phone ?? '') }}"
                                                                    class="form-control rounded-3"
                                                                    placeholder="Enter Phone Number"
                                                                    pattern="^\+?[0-9\s\(\)-]{5,20}$"
                                                                    title="Please enter a valid phone number">
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Email</strong>
                                                            <input type="email" name='email'
                                                                class="form-control rounded-3"
                                                                placeholder="Enter Email Address">
                                                        </div>
                                                    </div>

                                                    <h3 class="mt-5"><u>Nearest Stations</u></h3>


                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Station Name</strong>
                                                                <input type="text" name='station_name[]'
                                                                    class="form-control rounded-3" value=""
                                                                    placeholder="Station Name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group mt-3">
                                                                <strong>Time</strong>
                                                                <input type="text" name='time[]'
                                                                    class="form-control rounded-3"
                                                                    placeholder="15 minute walk">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group mt-3">
                                                            <strong>Duration</strong>
                                                            <input type="number" name='duration[]'
                                                                class="form-control rounded-3" placeholder="Duration">
                                                        </div>
                                                    </div>

                                                    <div id="station"></div>
                                                    <h4 id="addstButton" class="float-right"><u>Add Nearest
                                                            Station</u></h4>
                                                </div>

                                                <div class="form-group mt-3">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                    <a href="{{ route('hotels') }}" class="btn btn-danger">cancel</a>
                                                </div>

                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <!-- Start Select dateTime  -->
                    </div>

                </div>


            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    <script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addFaqButton = document.getElementById('addFaqButton');
            const faqContainer = document.getElementById('faq-container');
            const newFaqQuestion = document.getElementById('newFaqQuestion');
            const newFaqAnswer = document.getElementById('newFaqAnswer');

            addFaqButton.addEventListener('click', function() {
                const question = newFaqQuestion.value.trim();
                const answer = newFaqAnswer.value.trim();

                if (!question || !answer) {
                    alert('Please provide both Question and Answer.');
                    return;
                }

                // Create FAQ elements dynamically
                const questionDiv = document.createElement('div');
                questionDiv.classList.add('col-md-12', 'form-group', 'mt-3');
                questionDiv.innerHTML = `
            <strong>Question</strong>
            <textarea name="new_question[]" class="form-control rounded-3">${question}</textarea>
        `;

                const answerDiv = document.createElement('div');
                answerDiv.classList.add('col-md-12', 'form-group', 'mt-3');
                answerDiv.innerHTML = `
            <strong>Answer</strong>
            <textarea name="new_answer[]" class="form-control rounded-3">${answer}</textarea>
        `;

                // Append new elements to the container
                faqContainer.appendChild(questionDiv);
                faqContainer.appendChild(answerDiv);

                // Clear modal inputs
                newFaqQuestion.value = '';
                newFaqAnswer.value = '';

                // Close the modal
                const addFaqModal = document.getElementById('addFaqModal');
                const modalInstance = bootstrap.Modal.getInstance(addFaqModal);
                modalInstance.hide();
            });
        });
    </script>

    <script>
        // Handle adding reviews dynamically
        document.getElementById('addReviewButton').addEventListener('click', function() {
            const reviewContent = document.getElementById('newReviewContent').value.trim();
            const reviewRating = document.getElementById('newReviewRating').value.trim();

            // Validate input
            if (!reviewContent || !reviewRating || reviewRating < 1 || reviewRating > 5) {
                alert('Please provide a valid review and a rating between 1 and 5.');
                return;
            }

            // Append the review to the reviews container
            const reviewsContainer = document.getElementById('reviews-container');
            const newReviewDiv = document.createElement('div');
            newReviewDiv.classList.add('col-md-12', 'form-group', 'mt-3');
            newReviewDiv.innerHTML = `
            <strong>Review</strong>
            <textarea name="new_review[]" class="form-control rounded-3">${reviewContent}</textarea>
            <strong>Rating (out of 5)</strong>
            <input type="number" name="new_rating[]" value="${reviewRating}" class="form-control rounded-3" min="1" max="5">
            <hr>
        `;

            reviewsContainer.appendChild(newReviewDiv);

            // Clear modal inputs
            document.getElementById('newReviewContent').value = '';
            document.getElementById('newReviewRating').value = '';

            // Close the modal
            const addReviewModal = bootstrap.Modal.getInstance(document.getElementById('addReviewModal'));
            addReviewModal.hide();
        });
    </script>


</x-app-layout>
