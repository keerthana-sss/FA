<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Itinerary Added</title>
</head>
<body>
    <h2>New Itinerary Added to Trip: {{ $trip->title }}</h2>

    <p><strong>Title:</strong> {{ $title }}</p>
    <p><strong>Description:</strong> {{ $description ?? 'N/A' }}</p>
    <p><strong>Day:</strong> {{ $day_number }}</p>
    <p><strong>Start Time:</strong> {{ $start_time ?? 'N/A' }}</p>
    <p><strong>End Time:</strong> {{ $end_time ?? 'N/A' }}</p>
    <p><strong>Created/Edited by:</strong> {{ $user->name }}</p>

    <p>This is a notification for all members of the trip.</p>
</body>
</html>
