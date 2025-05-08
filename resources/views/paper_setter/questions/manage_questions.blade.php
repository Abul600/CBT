@extends('layouts.paper_setter')

@section('content')
<div class="container mt-4">
    <h2 class="text-xl font-bold mb-4">Manage Questions</h2>

    {{-- Success/Error Alerts --}}
    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger mb-4">{{ session('error') }}</div>
    @endif

    {{-- "Add Question" Button --}}
    <a 
        href="{{ route('paper_setter.questions.create') }}" 
        class="btn btn-primary mb-4"
        title="Add a new question"
    >
        <i class="fas fa-plus me-2"></i> Add Question
    </a>

    {{-- Questions Table --}}
    <form 
        method="POST" 
        action="{{ route('paper_setter.questions.sendToModerator') }}"
        onsubmit="return validateSubmission();"
    >
        @csrf

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th style="width: 40px;">
                        <input type="checkbox" id="select-all" title="Select all draft questions">
                    </th>
                    <th>ID</th>
                    <th>Question Text</th>
                    <th>Type</th>
                    <th>Marks</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($questions as $question)
                    <tr>
                        {{-- Checkbox (only for draft questions) --}}
                        <td>
                            @if ($question->status === 'draft')
                                <input 
                                    type="checkbox" 
                                    name="question_ids[]" 
                                    value="{{ $question->id }}"
                                    class="question-checkbox"
                                >
                            @endif
                        </td>

                        {{-- Question Details --}}
                        <td>{{ $question->id }}</td>
                        <td>{{ Str::limit($question->question_text, 70) }}</td>
                        <td>{{ ucfirst($question->type) }}</td>
                        <td>{{ $question->marks }}</td>

                        {{-- Status Badge --}}
                        <td>
                            @switch($question->status)
                                @case('sent')
                                    <span class="badge bg-info">Sent</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-success">Approved</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">Draft</span>
                            @endswitch
                        </td>

                        {{-- Delete Button --}}
                        <td>
                            <form 
                                action="{{ route('paper_setter.questions.destroy', $question->id) }}" 
                                method="POST"
                                onsubmit="return confirm('Delete this question permanently?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button 
                                    type="submit" 
                                    class="btn btn-sm btn-danger"
                                    title="Delete question"
                                >
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">No questions found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- "Send to Moderator" Button (only if draft questions exist) --}}
        @if($questions->where('status', 'draft')->count())
            <button 
                type="submit" 
                class="btn btn-primary mt-3"
                id="send-button"
            >
                <i class="fas fa-paper-plane me-2"></i> Send Selected to Moderator
            </button>
        @endif
    </form>
</div>

{{-- Scripts --}}
<script>
    // Select all checkboxes
    document.getElementById('select-all')?.addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.question-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Prevent submitting with no selection
    function validateSubmission() {
        const checked = document.querySelectorAll('.question-checkbox:checked');
        if (checked.length === 0) {
            alert('Please select at least one draft question to send.');
            return false;
        }
        return confirm('Send selected questions to moderator?');
    }
</script>
@endsection
