@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <h2 class="mb-4">Attraction Questions & Answers</h2>
                        
                        <div class="mb-4">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addQuestionModal">
                                <i class="fas fa-plus"></i> Add New Question
                            </button>
                        </div>
                        
                        @if(isset($questions) && count($questions) > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Question</th>
                                            <th>Answer</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($questions as $question)
                                            <tr>
                                                <td>{{ $question->id }}</td>
                                                <td>{{ $question->question }}</td>
                                                <td>{{ $question->answer }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-info edit-btn" 
                                                        data-id="{{ $question->id }}"
                                                        data-question="{{ $question->question }}"
                                                        data-answer="{{ $question->answer }}"
                                                        data-toggle="modal" data-target="#editQuestionModal">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-id="{{ $question->id }}"
                                                        data-toggle="modal" data-target="#deleteQuestionModal">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                No questions found for this attraction. Add a new question to get started.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addQuestionModalLabel">Add New Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('store_attraction_qa') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="sight_id" value="{{ $sightId }}">
                    
                    <div class="form-group">
                        <label for="question">Question</label>
                        <input type="text" class="form-control" id="question" name="question" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="answer">Answer</label>
                        <textarea class="form-control" id="answer" name="answer" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editQuestionModalLabel">Edit Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('update_attraction_qa') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="edit_question_id">
                    
                    <div class="form-group">
                        <label for="edit_question">Question</label>
                        <input type="text" class="form-control" id="edit_question" name="question" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_answer">Answer</label>
                        <textarea class="form-control" id="edit_answer" name="answer" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Question</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Question Modal -->
<div class="modal fade" id="deleteQuestionModal" tabindex="-1" role="dialog" aria-labelledby="deleteQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteQuestionModalLabel">Delete Question</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('delete_attraction_qa') }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="delete_question_id">
                    <p>Are you sure you want to delete this question? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Set up the edit modal
        $('.edit-btn').click(function() {
            var id = $(this).data('id');
            var question = $(this).data('question');
            var answer = $(this).data('answer');
            
            $('#edit_question_id').val(id);
            $('#edit_question').val(question);
            $('#edit_answer').val(answer);
        });
        
        // Set up the delete modal
        $('.delete-btn').click(function() {
            var id = $(this).data('id');
            $('#delete_question_id').val(id);
        });
    });
</script>
@endsection
