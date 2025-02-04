@extends('layout.mail', ['title' => $title])

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Welcome to AngleQuest {{ $detail['name'] }} 👋
            </h2>
            <p style="margin: 10px 0;">
                Congratulations! Your subscription is now active. You can start enjoying our {{ ucfirst($detail['service']) }} service
                right away. <br>
                Welcome onboard!
            </p>

        </td>
    </tr>


    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px; color: #999999;">
            <p>Thank you, <br> The AngleQuest Team</p>
        </td>
    </tr>
@endsection
