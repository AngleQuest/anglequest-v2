@extends('layout.mail')

@section('content')
    <tr>
        <td bgcolor="#ffffff" align="left"
            style="padding: 0px 30px 20px 30px; color: #666666; font-family: 'Lato', Helvetica, Arial, sans-serif;">
            <h2 style="margin: 0;">
                Dear {{ ucfirst($detail['name']) }}
            </h2>
            <p style="margin: 10px 0;">
                This email confirms that we have received your payment of <b>${{ number_format($detail['amount']) }}</b> for your Interiew
                Appointment with {{ ucfirst($detail['expert']) }}.
            </p>


        </td>
    </tr>

@endsection
