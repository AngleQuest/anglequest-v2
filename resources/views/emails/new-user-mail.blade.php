<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Angle Quest</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding: 10px 10px;
            background-color:#ffffff;
            color: rgba(0,0,0,0.1);
        }
        /* #16A34A; */
        .content {
            padding: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
        }
        .footer {
            text-align: center;
            padding: 10px 0;
            background-color: #191919;
            color: #ffff;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{asset($logo)}}" alt="company logo" />
        </div>
        <div class="content">
            {!! $email_content !!}
        </div>
        <div class="footer">
            <div class="footer">
                {!! $footer !!}
            </div>
        </div>
    </div>
</body>
</html>
