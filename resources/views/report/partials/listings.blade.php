<!-- resources/views/reports/partials/listings.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Listings Report</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Listings Report</h1>

    <table>
        <thead>
            <tr>
                <th>Region</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['Region'] }}</td>
                    <td>{{ $row['Type'] }}</td>
                    <td>{{ $row['Status'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
