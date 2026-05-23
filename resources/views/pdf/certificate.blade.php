<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Certificate of Completion</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background-color: #ffffff;
            color: {{ $settings->text_color ?? '#1a1a1a' }};
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        .outer-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 8px solid {{ $settings->primary_color ?? '#14215B' }};
        }

        .inner-border {
            position: absolute;
            top: 18px;
            left: 18px;
            right: 18px;
            bottom: 18px;
            border: 2px solid #d4af37;
        }

        .page {
            position: relative;
            width: 297mm;
            height: 210mm;
            padding: 36px 52px 28px 52px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            background-color: #ffffff;
        }

        /* ── Top section ── */
        .top-section {
            text-align: center;
            width: 100%;
            padding-top: 4px;
        }

        .org-name {
            font-size: 13px;
            font-weight: bold;
            letter-spacing: 4px;
            text-transform: uppercase;
            color: {{ $settings->primary_color ?? '#14215B' }};
            margin-bottom: 6px;
        }

        .header-divider {
            width: 80px;
            height: 2px;
            background-color: #d4af37;
            margin: 0 auto 10px auto;
        }

        .header-text {
            font-size: 34px;
            font-style: italic;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
            letter-spacing: 1px;
            line-height: 1.1;
        }

        /* ── Center section ── */
        .center-section {
            text-align: center;
            width: 100%;
        }

        .certify-text {
            font-size: 13px;
            font-style: italic;
            color: #555555;
            margin-bottom: 8px;
        }

        .student-name {
            font-size: 36px;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
            font-family: DejaVu Serif, Georgia, serif;
            font-style: italic;
            letter-spacing: 1px;
            margin-bottom: 8px;
            line-height: 1.1;
        }

        .name-underline {
            width: 340px;
            height: 1px;
            background-color: #d4af37;
            margin: 0 auto 10px auto;
        }

        .completed-text {
            font-size: 12px;
            color: #555555;
            margin-bottom: 6px;
            font-style: italic;
        }

        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
            margin-bottom: 8px;
            line-height: 1.2;
        }

        .body-text {
            font-size: 11px;
            color: #666666;
            font-style: italic;
            margin-bottom: 10px;
        }

        .details-row {
            font-size: 10px;
            color: #777777;
            letter-spacing: 0.5px;
        }

        .details-row span {
            margin: 0 10px;
        }

        .details-separator {
            color: #d4af37;
            margin: 0 4px;
        }

        /* ── Bottom section ── */
        .bottom-section {
            width: 100%;
        }

        .bottom-divider {
            width: 100%;
            height: 1px;
            background-color: #d4af37;
            margin-bottom: 12px;
        }

        .bottom-columns {
            width: 100%;
            display: table;
            table-layout: fixed;
        }

        .col-left,
        .col-center,
        .col-right {
            display: table-cell;
            vertical-align: top;
            width: 33.33%;
        }

        .col-left {
            text-align: left;
        }

        .col-center {
            text-align: center;
        }

        .col-right {
            text-align: right;
        }

        .cert-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #999999;
            margin-bottom: 2px;
        }

        .cert-value {
            font-size: 10px;
            font-family: DejaVu Sans Mono, Courier New, monospace;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
        }

        .cert-date {
            font-size: 10px;
            color: #555555;
            margin-top: 4px;
        }

        /* Seal circle */
        .seal-circle {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 3px solid {{ $settings->primary_color ?? '#14215B' }};
            background-color: #f8f8ff;
            margin: 0 auto;
            display: inline-block;
            line-height: 58px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
            letter-spacing: 1px;
        }

        .seal-outer {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 2px dashed #d4af37;
            margin: 0 auto;
            padding: 4px;
        }

        /* Signature */
        .signature-line {
            width: 140px;
            height: 1px;
            background-color: #333333;
            margin: 0 0 3px auto;
        }

        .signatory-name {
            font-size: 11px;
            font-weight: bold;
            color: {{ $settings->primary_color ?? '#14215B' }};
        }

        .signatory-title {
            font-size: 9px;
            color: #777777;
            margin-top: 2px;
        }

        /* ── Footer ── */
        .footer-section {
            text-align: center;
            width: 100%;
            padding-top: 6px;
        }

        .footer-text {
            font-size: 8px;
            color: #aaaaaa;
            margin-bottom: 2px;
        }

        .verification-url {
            font-size: 8px;
            color: #888888;
            font-family: DejaVu Sans Mono, Courier New, monospace;
        }

        .gold-dot {
            color: #d4af37;
            font-size: 14px;
            line-height: 1;
        }
    </style>
</head>
<body>
<div class="outer-border"></div>
<div class="inner-border"></div>

<div class="page">

    {{-- TOP SECTION --}}
    <div class="top-section">
        <div class="org-name">
            {{ $settings->organization_name ?? 'Mapelead Academy' }}
        </div>
        <div class="header-divider"></div>
        <div class="header-text">
            {{ $settings->header_text ?? 'Certificate of Completion' }}
        </div>
    </div>

    {{-- CENTER SECTION --}}
    <div class="center-section">
        <div class="certify-text">This is to certify that</div>
        <div class="student-name">{{ $certificate->user->full_name }}</div>
        <div class="name-underline"></div>
        <div class="completed-text">has successfully completed</div>
        <div class="course-name">{{ $certificate->course->title }}</div>
        <div class="body-text">
            {{ $settings->body_text ?? 'with distinction, having fulfilled all requirements of the program.' }}
        </div>
        <div class="details-row">
            <span>Attendance: <strong>{{ $certificate->attendance_percentage ?? 'N/A' }}%</strong></span>
            <span class="details-separator">&bull;</span>
            <span>Quiz Average: <strong>{{ $certificate->quiz_score_average ?? 'N/A' }}%</strong></span>
            <span class="details-separator">&bull;</span>
            <span>Issued: <strong>{{ $certificate->issued_at->format('F d, Y') }}</strong></span>
        </div>
    </div>

    {{-- BOTTOM SECTION --}}
    <div class="bottom-section">
        <div class="bottom-divider"></div>
        <div class="bottom-columns">
            {{-- LEFT: Certificate info --}}
            <div class="col-left">
                <div class="cert-label">Certificate No.</div>
                <div class="cert-value">{{ $certificate->certificate_number }}</div>
                <div class="cert-date">{{ $certificate->issued_at->format('d M Y') }}</div>
            </div>

            {{-- CENTER: Seal --}}
            <div class="col-center">
                <div class="seal-outer">
                    <div class="seal-circle">
                        {{ strtoupper(substr($settings->organization_name ?? 'Mapelead Academy', 0, 2)) }}
                    </div>
                </div>
            </div>

            {{-- RIGHT: Signature --}}
            <div class="col-right">
                <div class="signature-line"></div>
                <div class="signatory-name">{{ $settings->signatory_name ?? 'Director of Programs' }}</div>
                <div class="signatory-title">{{ $settings->signatory_title ?? 'Mapelead Academy' }}</div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer-section">
            @if($settings->footer_text)
            <div class="footer-text">{{ $settings->footer_text }}</div>
            @endif
            <div class="verification-url">Verify at: {{ $verificationUrl }}</div>
            <div class="footer-text" style="margin-top:2px;">
                Scan QR code or visit verification URL to verify authenticity
            </div>
        </div>
    </div>

</div>
</body>
</html>
