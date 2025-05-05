<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    <nav>
        <a href="{{ route('student.dashboard') }}">Home</a>
        <a href="#">Exams</a>
        <a href="#">Results</a>
        <a href="#">Study Materials</a>

        <!-- Logout Form -->
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" style="background:none; border:none; color:red; cursor:pointer;">Logout</button>
        </form>
    </nav>

    <div class="container">
        @yield('content')
    </div>

</body>
</html>
