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
                                <li class="breadcrumb-item" aria-current="page">View reviews</li>
                            </ul>
                        </div>
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h3 class="mb-0">Hotel Reviews</h3>
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
                                        @if ($message = Session::get('success'))
                                            <div class="col-md-8 alert alert-success mt-3">
                                                {{ $message }}
                                            </div>
                                        @endif
                                        @if ($errors->any())
                                            <div class="col-md-8 alert alert-danger mt-3">
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

                                        @if(isset($gethid[0]) && !empty($gethid[0]->name))
                                            <h3 class="mb-0 mt-3">{{$gethid[0]->name}}</h3>
                                        @else
                                            <h3 class="mb-0 mt-3">Manage Hotel</h3>
                                        @endif

                                        <div>
                                            <span class="hotel-review-option opt-0" data-value="0"
                                                  style="font-weight: bold; text-decoration: underline;">Pending |</span>
                                            <span class="hotel-review-option opt-1" data-value="1">Approved |</span>
                                            <span class="hotel-review-option opt-2" data-value="2">Disapproved |</span>
                                            <span class="hotel-review-option opt-3" data-value="3,4,5">Spam</span>
                                            <input type="hidden" name="" id="hotelid"
                                                   value="@if(!$gethid->isEmpty()) {{$gethid[0]->hotelid}} @endif">
                                        </div>

                                        <div class="row float-right" style="float:right">
                                            <div class="col-md-5 form-group">
                                                <select name="" id="sort_hotel_review" class="form-control">
                                                    <option value="asc">Sort Reviews</option>
                                                    <option value="desc">Newest</option>
                                                    <option value="asc">Oldest</option>
                                                </select>
                                            </div>
                                            <div class="col-md-7 form-group">
                                                <div class="form-search form-search-icon-right">
                                                    <input type="text" name="search_value" id="filterhotelbyid"
                                                           class="form-control rounded-3" placeholder="search by review id">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-xs-8 col-sm-8 col-md-8">
                                                    <div class="form-group mt-3 list-container">
                                                        @if(!$hotelreview->isEmpty())
                                                            @foreach($hotelreview as $value)
                                                                <!-- Review Form -->
                                                                <form action="{{ route('update_review', [$value->HotelReviewId]) }}" method="POST">
                                                                    @csrf
                                                                    <div>
                                                                        <textarea name="review" rows="7" class="form-control rounded-3" required>
                                                                            {{$value->Description}}
                                                                        </textarea>
                                                                    </div>
                                                                </form>
                                                            @endforeach
                                                        @else
                                                            <p>No Pending Reviews</p>
                                                        @endif
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
            </div>
        </div>
    </div>
</x-app-layout>
