<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bounty Awarded - HackHunt</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px;">
        <h2 style="color: #f59e0b;">Congratulations, {{ $user->name }}!</h2>
        <p>Your report on <strong>{{ $program->name }}</strong> has been accepted, and you've been awarded a bounty of:</p>
        <h3 style="color: #f59e0b;">${{ $bountyAmount }}</h3>
        <p>Thank you for making HackHunt safer!</p>
        <p>– The HackHunt Team</p>
    </div>
</body>
</html>
