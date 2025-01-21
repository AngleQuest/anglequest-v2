@extends('layout.mail', ['title' => $title])

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Welcome to AngleQuest 👋
            </h2>
            <p style="margin: 10px 0;">
                Thank you for joining our platform! Please Sign in via your email address to complete your registration.
                <br>
                Below are your login details as provided by {{ $detail['name'] }} to access your page to complete your profile
            </p>
            <p style="margin: 10px 0;">
                Email: {{ $detail['email'] }}
            </p>
            <p style="margin: 10px 0;">
                Password: {{ $detail['password'] }}
            </p>
        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px;">
            <a href="https://staging.anglequest.com/sign-in" class="button">Sign In</a>
        </td>
    </tr>
    <p style="margin: 10px 0;">
        If you did not create an account, no further action is required
    </p>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px; color: #999999;">
            <p>Thank you, <br> The AngleQuest Team</p>
        </td>
    </tr>
@endsection
