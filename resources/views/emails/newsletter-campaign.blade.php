@extends('layouts.email', ['title' => $title])

@section('content')
    <section style="margin:0 16px 16px 16px;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
            <tr><td align="center" style="font-size:15px;font-weight:600;padding-bottom:4px;">{{ $appName }}</td></tr>
            <tr><td align="center" style="font-size:32px;font-weight:800;line-height:1.2;color:#0b1c13;padding-bottom:4px;">📣 Campagne Newsletter</td></tr>
            <tr><td align="center" style="font-size:14px;font-weight:500;line-height:1.4;">Un nouveau message de notre équipe.</td></tr>
        </table>
    </section>

    <section style="background:#fff;border:1px solid #E3E3E1;border-radius:20px;padding:20px;margin:0 16px 0 16px;">
        <div style="color:#0b1c13;font-size:24px;font-weight:800;border-bottom:2px solid #0b1c13;padding-bottom:8px;margin-bottom:14px;">{{ $title }}</div>
        <div style="font-size:14px;line-height:1.55;color:#222;">
            {!! $content !!}
        </div>
    </section>

    <hr style="border:none;border-top:1px solid #E3E3E1;margin:16px 16px 0 16px;">

    @include('emails.partials.footer', [
        'appName' => $appName,
        'appUrl' => $appUrl,
        'contactEmail' => $contactEmail,
    ])
@endsection
