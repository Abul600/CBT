@extends('layouts.paper_setter')

@section('content')
<div class="container mt-4">
    <h2 class="text-xl font-bold mb-2">Manage Questions</h2>
    <p class="mb-4 text-sm text-gray-600">Here you can manage your exam questions.</p>

    <a href="{{ route('paper_setter.questions.create') }}" class="btn btn-primary mb-4">Add Question</a>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Form for sending selected questions --}}
    <form id="send-to-moderator-form" method="POST" action="{{ route('paper_setter.questions.sendToModerator') }}">
        @csrf

        <table class="table table-bordered border mt-4">
            <thead class="thead-light">
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Question Text</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr id="question-row-{{ $question->id }}">
                        <td>
                            @if (!$question->sent_to_moderator)
                                <input type="checkbox" class="question-checkbox" name="question_ids[]" value="{{ $question->id }}">
                            @endif
                        </td>
                        <td>{{ $question->id }}</td>
                        <td>{{ $question->title ?? '-' }}</td>
                        <td>{{ $question->question_text }}</td>
                        <td>
                            @if ($question->sent_to_moderator)
                                <span class="badge bg-success">Sent</span>
                            @else
                                <span class="badge bg-secondary">Not Sent</span>
                            @endif
                        </td>
                        <td>
                            @if (!$question->sent_to_moderator)
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteQuestion({{ $question->id }})">
                                    Delete
                                </button>
                            @else
                                <button class="btn btn-sm btn-secondary" type="button" disabled>Locked</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No questions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Submit Button Only if Questions Exist --}}
        @if($questions->where('sent_to_moderator', false)->count())
            <button type="submit" class="btn btn-primary mt-3" id="send-button">
                <i class="fas fa-paper-plane"></i> Send Selected to Moderator
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
</script>
@endsection
