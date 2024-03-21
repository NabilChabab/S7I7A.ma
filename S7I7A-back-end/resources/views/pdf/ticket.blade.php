<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1> {{$appointment->appointment_date}} </h1>
    <h1> {{$appointment->doctor->user->name}} </h1>
    <h1> {{$appointment->doctor->address}} </h1>
    <h1> {{$appointment->user->name}}</h1>
</body>
</html>
