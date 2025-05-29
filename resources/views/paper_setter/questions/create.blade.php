@extends('layouts.paper_setter')

@section('content')
    <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-yellow-500 mb-4 text-center">Create Question</h2>
    <p class="text-lg text-gray-600 text-center mb-6">Fill in the details below to create a new question. Questions will be reviewed and assigned to exams by moderators.</p>

    @if(session('success'))
        <div class="alert alert-success text-white bg-green-500 p-4 rounded-lg mb-4 shadow-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger text-white bg-red-500 p-4 rounded-lg mb-4 shadow-lg">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('paper_setter.questions.store') }}" method="POST">
        @csrf

        <!-- Question Type -->
        <div class="form-group mt-3">
            <label for="type" class="font-medium text-gray-700">Question Type:</label>
            <select name="type" id="type" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" required onchange="toggleQuestionType()">
                <option value="mcq1" {{ old('type') == 'mcq1' ? 'selected' : '' }}>MCQ Type 1 (1 Mark)</option>
                <option value="mcq2" {{ old('type') == 'mcq2' ? 'selected' : '' }}>MCQ Type 2 (2 Marks)</option>
                <option value="descriptive" {{ old('type') == 'descriptive' ? 'selected' : '' }}>Descriptive (Custom Marks)</option>
            </select>
        </div>

        <!-- Question Text -->
        <div class="form-group mt-2">
            <label for="question_text" class="font-medium text-gray-700">Question:</label>
            <textarea name="question_text" id="question_text" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" rows="4" required>{{ old('question_text') }}</textarea>
        </div>

        <!-- MCQ Options -->
        <div id="mcq_options" class="mt-3">
            <label class="font-medium text-gray-700 mb-2 block">Options:</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <input type="text" name="option_a" placeholder="Option A" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" value="{{ old('option_a') }}">
                <input type="text" name="option_b" placeholder="Option B" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" value="{{ old('option_b') }}">
                <input type="text" name="option_c" placeholder="Option C" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" value="{{ old('option_c') }}">
                <input type="text" name="option_d" placeholder="Option D" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" value="{{ old('option_d') }}">
            </div>
        </div>

        <!-- Correct Option -->
        <div id="correct_option_wrapper" class="form-group mt-3">
            <label for="correct_option" class="font-medium text-gray-700">Correct Option:</label>
            <select name="correct_option" id="correct_option" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full">
                <option value="">-- Select Correct Option --</option>
                <option value="a" {{ old('correct_option') == 'a' ? 'selected' : '' }}>Option A</option>
                <option value="b" {{ old('correct_option') == 'b' ? 'selected' : '' }}>Option B</option>
                <option value="c" {{ old('correct_option') == 'c' ? 'selected' : '' }}>Option C</option>
                <option value="d" {{ old('correct_option') == 'd' ? 'selected' : '' }}>Option D</option>
            </select>
            <small class="text-muted">Only applicable for MCQ type questions.</small>
        </div>

        <!-- Marks -->
        <div class="form-group mt-3">
            <label for="marks" class="font-medium text-gray-700">Marks:</label>
            <input type="number" name="marks" id="marks" class="form-control p-3 rounded-lg border-2 border-gray-300 w-full" min="1" max="100" value="{{ old('marks', 1) }}" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success w-full bg-gradient-to-r from-pink-500 to-yellow-500 text-white py-3 rounded-lg shadow-lg mt-6 transform hover:scale-105 transition-all duration-300">
            Create Question
        </button>
    </form>

    <script>
        function toggleQuestionType() {
            const type = document.getElementById("type").value;
            const mcqOptions = document.getElementById("mcq_options");
            const correctOption = document.getElementById("correct_option_wrapper");
            const marks = document.getElementById("marks");

            if (type === "descriptive") {
                mcqOptions.style.display = "none";
                correctOption.style.display = "none";
                marks.removeAttribute("readonly");
                if (!marks.value || marks.value === "1" || marks.value === "2") {
                    marks.value = "";
                }
            } else {
                mcqOptions.style.display = "block";
                correctOption.style.display = "block";
                marks.setAttribute("readonly", true);
                marks.value = (type === "mcq1") ? 1 : 2;
            }
        }

        document.addEventListener('DOMContentLoaded', toggleQuestionType);
    </script>
@endsection
