<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Lead Summary</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h2 style="text-align: center;">Lead Summary Report</h2>
    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Month</th>
                <th>Location</th>
                <th>Source</th>
                <th>Count</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    <td>{{ $row['Year'] }}</td>
                    <td>{{ $row['Month'] }}</td>
                    <td>{{ $row['Location'] }}</td>
                    <td>{{ $row['Source'] }}</td>
                    <td>{{ $row['Count'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
