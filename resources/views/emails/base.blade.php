<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $subject ?? config('app.name') }}</title>
<style>
  body { margin:0; padding:0; background:#f4f6fb; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; color:#1a1a2e; }
  .wrapper { max-width:600px; margin:32px auto; background:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
  .header { background:#1e3adb; padding:28px 40px; text-align:center; }
  .header img { max-height:48px; width:auto; }
  .header .app-name { color:#ffffff; font-size:22px; font-weight:700; letter-spacing:-0.3px; margin-top:8px; }
  .body { padding:36px 40px; font-size:15px; line-height:1.7; color:#374151; }
  .body h1,.body h2,.body h3 { color:#1a1a2e; margin-top:0; }
  .body a { color:#1e3adb; }
  .body .cta { display:inline-block; margin-top:20px; padding:12px 28px; background:#1e3adb; color:#ffffff!important; border-radius:8px; font-weight:600; text-decoration:none; font-size:14px; }
  .divider { border:none; border-top:1px solid #e5e7eb; margin:24px 0; }
  .footer { background:#f9fafb; padding:20px 40px; text-align:center; font-size:12px; color:#9ca3af; border-top:1px solid #e5e7eb; }
  .footer a { color:#6b7280; text-decoration:none; }
  @media(max-width:640px){
    .wrapper{margin:0;border-radius:0;}
    .header,.body,.footer{padding-left:20px;padding-right:20px;}
  }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    @if(!empty($logo_url))
    <img src="{{ $logo_url }}" alt="{{ $app_name ?? config('app.name') }}">
    @else
    <div class="app-name">{{ $app_name ?? config('app.name') }}</div>
    @endif
  </div>
  <div class="body">
    {!! $body_content !!}
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} {{ $app_name ?? config('app.name') }}. All rights reserved.<br>
    @if(!empty($support_email))
    Questions? <a href="mailto:{{ $support_email }}">{{ $support_email }}</a>
    @endif
  </div>
</div>
</body>
</html>
