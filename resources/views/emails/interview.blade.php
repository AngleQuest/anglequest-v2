@extends('layout.mail')

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Dear {{ ucfirst($detail['name']) }}
            </h2>
            <p style="margin: 10px 0;">
                This email confirms that we have received your payment of ${{ $detail['amount'] }} for your Interiew
                Appointment with {{ ucfirst($detail['expert']) }}.
            </p>


        </td>
    </tr>

    <tr>
        <td bgcolor="#ffffff" align="center" style="padding: 20px; color: #999999;">
            <p>Thank you, <br> The AngleQuest Team</p>
        </td>
    </tr>
@endsection
