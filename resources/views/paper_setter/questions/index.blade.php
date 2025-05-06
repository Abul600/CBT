@extends('layouts.paper_setter')

@section('content')
<div class="container mt-4">
    <!-- Title: Centered alignment -->
    <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-yellow-500 mb-4 text-center">Manage Questions</h2>

    <!-- Description: Centered alignment -->
    <p class="mb-6 text-lg text-gray-600 text-center">Here you can manage your exam questions with ease.</p>

    <!-- Add Question Button: Centered alignment -->
    <div class="text-center mb-4">
        <a href="{{ route('paper_setter.questions.create') }}" class="btn btn-primary bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white hover:shadow-xl transition-all duration-300 ease-in-out py-2 px-6 rounded-full">
            <span class="text-lg font-semibold">Add Question</span>
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success text-white bg-green-500 p-4 rounded-lg mb-4 shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger text-white bg-red-500 p-4 rounded-lg mb-4 shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    {{-- Form for sending selected questions --}}
    <form id="send-to-moderator-form" method="POST" action="{{ route('paper_setter.questions.sendToModerator') }}" x-data="{ isSubmitting: false }" @submit.prevent="isSubmitting = true">
        @csrf

        <table class="table table-bordered border mt-6 w-full bg-gradient-to-br from-gray-100 to-white dark:from-gray-900 dark:to-black shadow-lg rounded-lg">
            <thead class="thead-light bg-gradient-to-r from-blue-500 to-purple-600 text-white text-center">
                <tr>
                    <th><input type="checkbox" id="select-all" class="transform scale-125"></th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Question Text</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr id="question-row-{{ $question->id }}" class="hover:bg-blue-50 dark:hover:bg-blue-800 transition-colors duration-300">
                        <td class="text-center">
                            @if (!$question->sent_to_moderator)
                                <input type="checkbox" class="question-checkbox transform scale-125" name="question_ids[]" value="{{ $question->id }}">
                            @endif
                        </td>
                        <td class="text-center">{{ $question->id }}</td>
                        <td>{{ $question->title ?? '-' }}</td>
                        <td>{{ $question->question_text }}</td>
                        <td class="text-center">
                            @if ($question->sent_to_moderator)
                                <span class="badge bg-success text-green-700 py-1 px-3 rounded-full shadow-lg transition-all duration-300">Sent</span>
                            @else
                                <span class="badge bg-secondary text-gray-700 py-1 px-3 rounded-full shadow-lg">Not Sent</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!$question->sent_to_moderator)
                                <button type="button" class="btn btn-sm bg-red-600 text-white hover:bg-red-700 py-2 px-4 rounded-full transition-transform transform hover:scale-105" onclick="deleteQuestion({{ $question->id }})">
                                    Delete
                                </button>
                            @else
                                <button class="btn btn-sm bg-gray-400 text-white py-2 px-4 rounded-full cursor-not-allowed" type="button" disabled>Locked</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-xl text-gray-500 py-4">No questions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Submit Button Only if Questions Exist --}}
        @if($questions->where('sent_to_moderator', false)->count())
            <button type="submit" class="btn btn-primary mt-3 w-full bg-gradient-to-r from-pink-500 to-yellow-500 text-white py-3 rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105 hover:shadow-xl" id="send-button" x-bind:disabled="isSubmitting">
                <span x-show="!isSubmitting"><i class="fas fa-paper-plane"></i> Send Selected to Moderator</span>
                <span x-show="isSubmitting">Sending...</span>
            </button>
        @endif
    </form>
</div>

{{-- Scripts --}}
<script>
    // Select all checkboxes
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.question-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // Delete question with AJAX
    function deleteQuestion(id) {
        if (confirm('Are you sure you want to delete this question?')) {
            fetch('{{ url("paper_setter/questions") }}/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (response.ok) {
                    document.getElementById('question-row-' + id).remove();
                    alert('Question deleted successfully.');
                } else {
                    alert('Failed to delete question.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Something went wrong.');
            });
        }
    }

    // Confirm before sending
    document.getElementById('send-to-moderator-form')?.addEventListener('submit', function (event) {
        const selected = document.querySelectorAll('.question-checkbox:checked');
        if (selected.length === 0) {
            event.preventDefault();
            alert('Please select at least one question to send.');
        } else {
            if (!confirm('Are you sure you want to send the selected questions to the moderator?')) {
                event.preventDefault();
            }
        }
    });
</script>

@endsection
