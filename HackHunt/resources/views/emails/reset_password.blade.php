<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Your Password - HackHunt</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f6f8; padding: 30px;">
    <div style="background-color: #ffffff; padding: 30px; border-radius: 10px;">
        <h2 style="color: #4f46e5;">Reset Your Password</h2>
        <p>Hello {{ $data['name'] }},</p>
        <p>We received a request to reset your HackHunt account password. Click the button below to proceed:</p>
        <a href="{{ $data['resetLink'] }}" style="background-color: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Reset Password</a>
        <p>If you didn’t request this, you can safely ignore this email.</p>
        <p>– The HackHunt Team</p>
    </div>
</body>
</html>
