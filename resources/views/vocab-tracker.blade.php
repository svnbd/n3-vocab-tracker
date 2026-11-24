<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mock-test-url" content="{{ route('progress.mock') }}">
    <title>JLPT N3 Vocabulary Tracker</title>
</head>
<body>
    <h1>JLPT N3 Vocabulary Tracker</h1>
    <p>Completed Days: <span id="completed-days-count">{{ $completedDays ?? 0 }}</span> / 60</p>
    <p>Progress: <span id="overall-percentage">{{ $percentage ?? 0 }}%</span></p>
</body>
</html>