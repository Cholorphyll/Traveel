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
                <li class="breadcrumb-item" aria-current="page">Hotel Q&A</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">HOTEL Q&A MANAGEMENT</h2>
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
                    <br>
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary float-right mb-3" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                      Add New Question
                    </button>

                    <div class="table-responsive">
                      <table class="table table-bordered">
                        <thead>
                          <tr>
                            <th>HotelID</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                         
                          @if(count($questions) > 0)
                            @foreach($questions as $question)
                              <tr>
                                <td>{{ $question->HotelId }}</td>
                                <td>{{ $question->Question }}</td>
                                <td>{{ Str::limit($question->Answer, 100) }}</td>
                                <td>
                                  <button type="button" class="btn btn-sm btn-primary edit-question" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editQuestionModal" 
                                    data-id="{{ $question->HotelId }}"
                                    data-question="{{ $question->Question }}"
                                    data-answer="{{ $question->Answer }}">
                                    Edit
                                  </button>
                                  <form action="{{ route('delete_hotel_qa') }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="hotelQuestionId" value="{{ $question->HotelId }}">
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                  </form>
                                </td>
                              </tr>
                            @endforeach
                          @else
                            <tr>
                              <td colspan="4" class="text-center">No questions found</td>
                            </tr>
                          @endif
                        </tbody>
                      </table>
                    </div>

                    <!-- Add Question Modal -->
                    <div class="modal fade" id="addQuestionModal" tabindex="-1" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form action="{{ route('store_hotel_qa') }}" method="POST">
                            @csrf
                            <div class="modal-body">
                              <input type="hidden" name="hotel_id" value="{{ $hotelId }}">
                              <div class="mb-3">
                                <label for="question" class="form-label">Question</label>
                                <input type="text" class="form-control" id="question" name="Question" required>
                              </div>
                              <div class="mb-3">
                                <label for="answer" class="form-label">Answer</label>
                                <textarea class="form-control" id="answer" name="Answer" rows="5" required></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Save Question</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                    <!-- Edit Question Modal -->
                    <div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <form action="{{ route('update_hotel_qa') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                              <input type="hidden" id="edit_question_id" name="hotelQuestionId">
                              <div class="mb-3">
                                <label for="edit_question" class="form-label">Question</label>
                                <input type="text" class="form-control" id="edit_question" name="Question" required>
                              </div>
                              <div class="mb-3">
                                <label for="edit_answer" class="form-label">Answer</label>
                                <textarea class="form-control" id="edit_answer" name="Answer" rows="5" required></textarea>
                              </div>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                              <button type="submit" class="btn btn-primary">Update Question</button>
                            </div>
                          </form>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Set up edit question modal
      const editButtons = document.querySelectorAll('.edit-question');
      editButtons.forEach(button => {
        button.addEventListener('click', function() {
          const id = this.getAttribute('data-id');
          const question = this.getAttribute('data-question');
          const answer = this.getAttribute('data-answer');
          
          document.getElementById('edit_question_id').value = id;
          document.getElementById('edit_question').value = question;
          document.getElementById('edit_answer').value = answer;
        });
      });
    });
  </script>
</x-app-layout>
