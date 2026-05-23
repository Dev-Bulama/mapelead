<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Admission Slip — {{ $user->admission_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background-color: #f5f6fa;
            color: #1a1a1a;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        /* ── Page wrapper ── */
        .page {
            width: 297mm;
            height: 210mm;
            background-color: #ffffff;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* ── Header bar ── */
        .header-bar {
            background-color: #14215B;
            padding: 14px 30px;
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .header-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }

        .header-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }

        .org-name {
            font-size: 11px;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #ffffff;
            opacity: 0.85;
            margin-bottom: 3px;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #ffffff;
            letter-spacing: 1px;
            line-height: 1.2;
        }

        .doc-subtitle {
            font-size: 9px;
            color: rgba(255,255,255,0.6);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        .header-badge {
            display: inline-block;
            background-color: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 6px 12px;
            text-align: center;
        }

        .header-badge-label {
            font-size: 8px;
            color: rgba(255,255,255,0.65);
            text-transform: uppercase;
            letter-spacing: 1px;
            display: block;
            margin-bottom: 2px;
        }

        .header-badge-value {
            font-size: 14px;
            font-weight: bold;
            color: #ffffff;
            font-family: DejaVu Sans Mono, Courier New, monospace;
            letter-spacing: 2px;
        }

        /* ── Body ── */
        .body {
            flex: 1;
            display: table;
            width: 100%;
            table-layout: fixed;
            padding: 0;
        }

        /* ── Left panel ── */
        .panel-left {
            display: table-cell;
            vertical-align: top;
            width: 34%;
            background-color: #f8f9fc;
            border-right: 2px solid #e8eaf0;
            padding: 22px 20px;
            text-align: center;
        }

        .student-photo {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #14215B;
            margin: 0 auto 14px auto;
            display: block;
        }

        .student-name-large {
            font-size: 15px;
            font-weight: bold;
            color: #14215B;
            margin-bottom: 6px;
            line-height: 1.25;
        }

        .admission-box {
            border: 2px solid #14215B;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 10px auto;
            display: inline-block;
            background-color: #f0f2f9;
        }

        .admission-box-label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 3px;
        }

        .admission-box-number {
            font-family: DejaVu Sans Mono, Courier New, monospace;
            font-size: 16px;
            font-weight: bold;
            color: #14215B;
            letter-spacing: 2px;
        }

        .left-divider {
            width: 60px;
            height: 2px;
            background-color: #14215B;
            margin: 12px auto;
            opacity: 0.3;
        }

        .left-meta {
            font-size: 10px;
            color: #555;
            margin-bottom: 5px;
            line-height: 1.4;
        }

        .left-meta strong {
            color: #14215B;
            font-size: 11px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-completed {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-pending {
            background-color: #fef3c7;
            color: #92400e;
        }

        /* ── Right panel ── */
        .panel-right {
            display: table-cell;
            vertical-align: top;
            width: 66%;
            padding: 20px 28px 16px 24px;
        }

        .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #14215B;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #14215B;
        }

        /* ── Details table ── */
        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table tr {
            border-bottom: 1px solid #f0f2f8;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 5px 6px;
            font-size: 10px;
            line-height: 1.4;
            vertical-align: top;
        }

        .td-label {
            color: #888;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 36%;
            font-size: 8.5px;
        }

        .td-value {
            color: #1a1a1a;
            font-weight: normal;
            padding-left: 8px;
        }

        .td-value strong {
            font-weight: bold;
            color: #14215B;
        }

        .td-badge-paid {
            display: inline-block;
            background-color: #d1fae5;
            color: #065f46;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .td-badge-pending {
            display: inline-block;
            background-color: #fef3c7;
            color: #92400e;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ── Verification section ── */
        .verification-section {
            margin-top: 14px;
            padding: 10px 12px;
            background-color: #f8f9fc;
            border: 1px solid #e2e5ef;
            border-radius: 6px;
        }

        .verification-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #888;
            margin-bottom: 4px;
            font-weight: bold;
        }

        .verification-url {
            font-family: DejaVu Sans Mono, Courier New, monospace;
            font-size: 9px;
            color: #14215B;
            word-break: break-all;
            font-weight: bold;
        }

        .verification-note {
            font-size: 8px;
            color: #aaa;
            margin-top: 3px;
            font-style: italic;
        }

        /* ── Bottom bar ── */
        .bottom-bar {
            background-color: #14215B;
            padding: 9px 30px;
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .bottom-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
        }

        .bottom-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            width: 40%;
        }

        .bottom-disclaimer {
            font-size: 8px;
            color: rgba(255,255,255,0.65);
            font-style: italic;
        }

        .bottom-issued {
            font-size: 8px;
            color: rgba(255,255,255,0.5);
        }

        /* ── Signature area ── */
        .signature-area {
            margin-top: 10px;
        }

        .signature-line {
            width: 120px;
            height: 1px;
            background-color: #999;
            margin-bottom: 3px;
        }

        .signature-name {
            font-size: 9px;
            font-weight: bold;
            color: #14215B;
        }

        .signature-title {
            font-size: 8px;
            color: #888;
        }

        /* Outer border accent */
        .accent-top {
            height: 4px;
            background-color: #d4af37;
            width: 100%;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- Gold accent strip --}}
    <div class="accent-top"></div>

    {{-- ── HEADER BAR ── --}}
    <div class="header-bar">
        <div class="header-left">
            <div class="org-name">{{ $settings->organization_name ?? 'Mapelead Technology Academy' }}</div>
            <div class="doc-title">Official Student Admission Slip</div>
            <div class="doc-subtitle">Academic Year {{ now()->format('Y') }}</div>
        </div>
        <div class="header-right">
            @if($user->admission_number)
            <div class="header-badge">
                <span class="header-badge-label">Admission No.</span>
                <span class="header-badge-value">{{ $user->admission_number }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── BODY ── --}}
    <div class="body">

        {{-- LEFT PANEL --}}
        <div class="panel-left">

            {{-- Student Photo --}}
            <img src="{{ $user->avatar_url }}"
                 alt="{{ $user->full_name }}"
                 class="student-photo">

            {{-- Name --}}
            <div class="student-name-large">{{ $user->full_name }}</div>

            {{-- Email --}}
            <div class="left-meta" style="font-size:9px; color:#777;">{{ $user->email }}</div>

            {{-- Admission number box --}}
            @if($user->admission_number)
            <div class="admission-box">
                <div class="admission-box-label">Admission Number</div>
                <div class="admission-box-number">{{ $user->admission_number }}</div>
            </div>
            @endif

            <div class="left-divider"></div>

            {{-- Course --}}
            @if($enrollment)
            <div class="left-meta">
                <strong>{{ $enrollment->course->title }}</strong>
            </div>

            {{-- Training type --}}
            <div class="left-meta">
                {{ ucfirst($enrollment->course->type ?? 'Online') }} Training
            </div>

            {{-- Batch --}}
            @if($enrollment->batch && $enrollment->batch->batch)
            <div class="left-meta">
                Batch: <strong>{{ $enrollment->batch->batch->name }}</strong>
            </div>
            @endif

            {{-- Status badge --}}
            @php $st = $enrollment->status ?? 'pending'; @endphp
            <span class="status-badge {{ $st === 'active' ? 'status-active' : ($st === 'completed' ? 'status-completed' : 'status-pending') }}">
                {{ $st === 'active' ? 'Admitted / Active' : ucfirst($st) }}
            </span>
            @endif

        </div>

        {{-- RIGHT PANEL --}}
        <div class="panel-right">

            <div class="section-title">Student Details</div>

            <table class="details-table">
                <tr>
                    <td class="td-label">Admission No.</td>
                    <td class="td-value"><strong>{{ $user->admission_number ?? '—' }}</strong></td>
                    <td class="td-label">Full Name</td>
                    <td class="td-value">{{ $user->full_name }}</td>
                </tr>
                <tr>
                    <td class="td-label">Email</td>
                    <td class="td-value">{{ $user->email }}</td>
                    <td class="td-label">Phone</td>
                    <td class="td-value">{{ $user->phone ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="td-label">Gender</td>
                    <td class="td-value">{{ ucfirst($user->gender ?? '—') }}</td>
                    <td class="td-label">Date of Birth</td>
                    <td class="td-value">{{ $user->date_of_birth ? $user->date_of_birth->format('F j, Y') : '—' }}</td>
                </tr>
                <tr>
                    <td class="td-label">Course</td>
                    <td class="td-value" colspan="3">
                        <strong>{{ $enrollment->course->title ?? '—' }}</strong>
                    </td>
                </tr>
                <tr>
                    <td class="td-label">Training Type</td>
                    <td class="td-value">{{ ucfirst($enrollment->course->type ?? '—') }}</td>
                    <td class="td-label">Batch</td>
                    <td class="td-value">
                        {{ ($enrollment->batch && $enrollment->batch->batch) ? $enrollment->batch->batch->name : '—' }}
                    </td>
                </tr>
                <tr>
                    <td class="td-label">Enrolled On</td>
                    <td class="td-value">
                        {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('F j, Y') : '—' }}
                    </td>
                    <td class="td-label">Status</td>
                    <td class="td-value"><strong>{{ ucfirst($enrollment->status ?? '—') }}</strong></td>
                </tr>
                <tr>
                    <td class="td-label">Payment Status</td>
                    <td class="td-value">
                        @if(($enrollment->payment_status ?? '') === 'paid')
                            <span class="td-badge-paid">Paid</span>
                        @else
                            <span class="td-badge-pending">{{ ucfirst($enrollment->payment_status ?? 'pending') }}</span>
                        @endif
                    </td>
                    <td class="td-label">Issued By</td>
                    <td class="td-value">{{ $settings->signatory_name ?? 'Director of Programs' }}</td>
                </tr>
            </table>

            {{-- Verification section --}}
            <div class="verification-section">
                <div class="verification-label">Verify This Admission Online</div>
                <div class="verification-url">{{ $verificationUrl }}</div>
                <div class="verification-note">Visit the URL above to verify the authenticity of this admission slip.</div>
            </div>

            {{-- Signature --}}
            <div class="signature-area">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $settings->signatory_name ?? 'Director of Programs' }}</div>
                <div class="signature-title">{{ $settings->signatory_title ?? ($settings->organization_name ?? 'Mapelead Academy') }}</div>
            </div>

        </div>

    </div>{{-- /body --}}

    {{-- ── BOTTOM BAR ── --}}
    <div class="bottom-bar">
        <div class="bottom-left">
            <div class="bottom-disclaimer">
                This is an official admission document. Valid for the duration of the enrolled course.
                Any alteration renders this document invalid.
            </div>
        </div>
        <div class="bottom-right">
            <div class="bottom-issued">
                Date Issued: {{ now()->format('F j, Y') }}
            </div>
        </div>
    </div>

</div>
</body>
</html>
