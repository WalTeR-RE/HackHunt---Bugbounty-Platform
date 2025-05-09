<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Bounty Awarded - HackHunt</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #0f0f1b;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #e4e4e7;
    }

    .container {
      max-width: 600px;
      margin: 40px auto;
      background: linear-gradient(to bottom right, #1a1a2e, #11111b);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 30px rgba(111, 14, 190, 0.2);
      border: 1px solid rgba(111, 14, 190, 0.2);
    }

    .header {
      background: linear-gradient(to right, #6f0ebe, #a855f7);
      padding: 24px;
      text-align: center;
    }

    .header h1 {
      margin: 0;
      font-size: 26px;
      color: #ffffff;
      letter-spacing: 1px;
    }

    .content {
      padding: 30px 24px;
      text-align: center;
    }

    .content h2 {
      color: #c084fc;
      margin-bottom: 10px;
      font-size: 22px;
    }

    .content p {
      font-size: 16px;
      line-height: 1.6;
      color: #d1d5db;
      margin-bottom: 20px;
    }

    .bounty-box {
      background-color: #181825;
      border: 1px solid #6f0ebe;
      border-radius: 12px;
      padding: 16px;
      margin: 20px auto;
      display: inline-block;
      color: #ffffff;
    }

    .bounty-box h3 {
      margin: 0;
      font-size: 22px;
      font-weight: bold;
      color: #c084fc;
    }

    .footer {
      padding: 20px;
      font-size: 14px;
      text-align: center;
      color: #888;
    }

    @media only screen and (max-width: 600px) {
      .content {
        padding: 20px 16px;
      }

      .header h1 {
        font-size: 22px;
      }

      .content h2 {
        font-size: 20px;
      }

      .bounty-box h3 {
        font-size: 20px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <h1>🎉 Bounty Awarded!</h1>
    </div>
    <div class="content">
      <h2>Congratulations, {{ $data['name'] }}!</h2>
      <p>Your report <strong>“{{ $data['title'] }}”</strong> submitted to <strong>{{ $data['program_name'] }}</strong> has been accepted.</p>
      <p>You've earned:</p>

      <div class="bounty-box">
        <h3>{{ $data['points'] }} Points</h3>
        <h3>{{ $data['bounty'] }}$</h3>
      </div>

      <p>Thank you for helping make HackHunt safer and more secure.</p>
      <p>Keep hacking, stay awesome.</p>
    </div>
    <div class="footer">
      – The HackHunt Team
    </div>
  </div>
</body>
</html>
