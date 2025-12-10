<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Dashboard - {{ $trip->title }}</title>
    
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container my-5">
        <h2 class="mb-4">Trip Dashboard: {{ $trip->title }}</h2>

        {{-- Big Number Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Members</h5>
                        <p class="card-text display-4">{{ $totalMembers }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Expense</h5>
                        <p class="card-text display-4">₹{{ number_format($totalExpense, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Files</h5>
                        <p class="card-text display-4">{{ array_sum($fileData) }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        <div class="row">
            {{-- Payer vs Contribution --}}
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Payer vs Total Contribution</div>
                    <div class="card-body">
                        <canvas id="payerChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- File Uploads Pie Chart --}}
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">File Uploads by Type</div>
                    <div class="card-body">
                        <canvas id="fileChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Payer vs Contribution Bar Chart
        const payerCtx = document.getElementById('payerChart').getContext('2d');
        const payerChart = new Chart(payerCtx, {
            type: 'bar',
            data: {
                labels: @json($payerData->pluck('name')),
                datasets: [{
                    label: 'Amount Contributed',
                    data: @json($payerData->pluck('amount')),
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // File Uploads Pie Chart
        const fileCtx = document.getElementById('fileChart').getContext('2d');
        const fileChart = new Chart(fileCtx, {
            type: 'pie',
            data: {
                labels: @json(array_keys($fileData)),
                datasets: [{
                    label: 'File Count',
                    data: @json(array_values($fileData)),
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'
                    ],
                }]
            },
            options: {
                responsive: true,
            }
        });
    </script>
</body>
</html>
