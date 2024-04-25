<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Email</title>
</head>
<body>
    <h2>Welcome to Our Platform, {{ $user->name }}!</h2>
    <p>Your account has been successfully created. Here are your login credentials:</p>
    <p>Email: {{ $user->email }}</p>
    <p>Password: {{ $password }}</p>
    <p>Please keep this information secure and do not share it with anyone.</p>
    <p>Thank you for joining us!</p>
</body>
</html>
