<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Expense Added</title>
</head>
<body>
    <h2>New Expense Added in Trip: {{ $trip->title }}</h2>

    <p><strong>Payer:</strong> {{ $payer->name }}</p>
    <p><strong>Payee:</strong> {{ $payee->name }}</p>
    <p><strong>Amount:</strong> ₹{{ number_format($amount, 2) }}</p>

    @if(!empty($description))
        <p><strong>Description:</strong> {{ $description }}</p>
    @endif

    <p>This is a notification to inform you that a new expense has been recorded.</p>
</body>
</html>
