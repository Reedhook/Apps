<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сброс пароля</title>
</head>
<body>
<h2>Сброс пароля</h2>
<form method="POST" action="{{ route('password.update') }}">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <label for="email">Email Address:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">New Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <label for="password_confirmation">Confirm New Password:</label><br>
    <input type="password" id="password_confirmation" name="password_confirmation" required><br><br>

    <button type="submit">Reset Password</button>
</form>
</body>
</html>
