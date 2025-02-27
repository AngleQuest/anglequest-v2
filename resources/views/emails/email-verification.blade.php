@extends('layout.mail', ['title' => 'Email verification'])

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Welcome to AngleQuest 👋
            </h2>
            <p>Kindly use the code below for your email verification process:</p>
            <h2 style="text-align: center">{{ $user->email_code }}</h2>

        </td>
    </tr>
    <tr>
        <td bgcolor="#0d468b" align="center" style="padding: 20px;color:white">
            <a href=" https://dev.anglequest.com/verify-email" class="button">Verify Email</a>
        </td>
    </tr>
@endsection
