<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescription</title>
    <style>
        /* Add your CSS styles here for formatting the PDF */
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .prescription-details {
            margin-bottom: 20px;
        }
        .prescription-details p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Prescription</h1>
    <div class="prescription-details">
        <p><strong>Medication:</strong> {{ $prescription->medication }}</p>
        <p><strong>Dosage:</strong> {{ $prescription->dosage }}</p>
        <p><strong>Instructions:</strong> {{ $prescription->instructions }}</p>
        <p><strong>Instructions:</strong> {{ $prescription->instructions }}</p>
    </div>
</body>
</html>
