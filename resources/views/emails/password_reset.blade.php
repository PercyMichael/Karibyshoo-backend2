<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Password Reset Request</title>
    </head>

    <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f7fafc; text-align: center;">
        <!-- Logo above the container -->
        <img src="https://example.com/images/logo.png" alt="Karibyshoo"
            style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 20px;">

        <div
            style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">
            <div style="padding: 20px; background-color: #4CAF50; color: #ffffff; border-radius: 8px 8px 0 0;">
                <h1 style="margin: 0; font-size: 24px;">Password Reset Email</h1>
            </div>
            <div style="padding: 20px; text-align: center;">
                <p>Hello <span style="color: #4CAF50;">{{ ucwords($user->name) }}</span>,</p>
                <p>We received a request to reset your password for your account.</p>
                <p>If you did not request this, please ignore this email.</p>
                <p>Your reset code is: <strong style="color: #4CAF50;">{{ $resetPasswordToken }}</strong></p>
                <p>Use this code to reset your password.</p>
                <p>If you did not request this change, please let us know.</p>
            </div>
            <div style="padding: 10px; font-size: 12px; color: #777; border-top: 1px solid #eeeeee;">
                <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved.</p>
                <p>Contact us: <a href="mailto:{{ config('app.contact_email') }}"
                        style="color: #4CAF50;">{{ config('app.contact_email') }}</a></p>
                <p>Phone: <a href="tel:{{ config('app.contact_phone') }}"
                        style="color: #4CAF50;">{{ config('app.contact_phone') }}</a></p>
            </div>
        </div>
    </body>

</html>