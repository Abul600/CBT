@extends('layouts.paper_setter')

@section('content')
    <h2>Create Question</h2>
    <p>Fill in the details below to create a new question.</p>

    <form action="{{ route('paper_setter.questions.store') }}" method="POST">
        @csrf

        <div>
            <label for="question_text">Question:</label>
            <textarea name="question_text" id="question_text" rows="4" required></textarea>
        </div>

        <div>
            <label for="option_a">Option A:</label>
            <input type="text" name="option_a" id="option_a" required>
        </div>

        <div>
            <label for="option_b">Option B:</label>
            <input type="text" name="option_b" id="option_b" required>
        </div>

        <div>
            <label for="option_c">Option C:</label>
            <input type="text" name="option_c" id="option_c" required>
        </div>

        <div>
            <label for="option_d">Option D:</label>
            <input type="text" name="option_d" id="option_d" required>
        </div>

        <div>
            <label for="correct_option">Correct Answer:</label>
            <select name="correct_option" id="correct_option" required>
                <option value="A">Option A</option>
                <option value="B">Option B</option>
                <option value="C">Option C</option>
                <option value="D">Option D</option>
            </select>
        </div>

        <button type="submit">Create Question</button>
    </form>
@endsection
