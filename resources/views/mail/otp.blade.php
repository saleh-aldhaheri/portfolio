<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Verification code</title>
    </head>
    <body>
        <p>Hello,</p>

        @if ($purpose === 'admin-email-verification')
            <p>Use the following code to verify your admin account email address:</p>
        @else
            <p>Use the following code to verify your email for the contact form:</p>
        @endif

        <p style="font-size: 28px; font-weight: bold; letter-spacing: 4px;">{{ $code }}</p>

        <p>This code expires in 10 minutes.</p>

        <p>If you did not request this, you can safely ignore this email.</p>
    </body>
</html>
