<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>You've Been Invited - HackHunt</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px;">
        <h2 style="color: #10b981;">You're Invited!</h2>
        <p>Hello {{ $data['name'] }},</p>
        <p>You’ve been invited to join a security program on HackHunt.</p>
        <p>Program: <strong>{{ $data['program_name'] }}</strong></p>
        <p>Click below to accept the invitation and start hunting!</p>
        <a href="{{ $data['link'] }}" style="background-color: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Accept Invitation</a>
        <p>Happy hunting!</p>
        <p>– The HackHunt Team</p>
    </div>
</body>
</html>
