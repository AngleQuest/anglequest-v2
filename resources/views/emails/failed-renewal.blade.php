@extends('layout.mail', ['title' => $title])

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Hello {{ $detail['name'] }}
            </h2>
            <p style="margin: 10px 0;">
                Your recent subscription payment failed. Please update your billing information to
                ensure uninterrupted service. <br>
                If you need help, contact us at <a href="mailto:ask@anglequest.com;">Support Email</a>.

            </p>

        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px; color: #999999;">
            <p>Thank you, <br> The AngleQuest Team</p>
        </td>
    </tr>
@endsection
