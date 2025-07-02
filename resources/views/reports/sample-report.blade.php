<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12pt; }
        .header { text-align: center; margin-bottom: 20px; }
        .section-title { font-weight: bold; margin-top: 20px; }
        .footer {
            font-size: 10pt;
            text-align: center;
            border-top: 1px solid #000;
            padding-top: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<!-- 
<div class="header">
    <h5>[ NationalRad Sample Body Radiology Report ]</h5>
    <img src="{{ public_path('images/sure.png') }}" width="150">
</div> -->
<!-- <div style="text-align: center; padding-bottom: 10px;">
    <div style="font-size: 12pt; font-weight: bold; margin-bottom: 20px;">
        [ NationalRad Sample Body Radiology Report ]
    </div>
    <img src="{{ asset('app/public/images/sure.png') }}" width="120" style="margin-bottom: 5px;">
</div> -->



<p>
    Imaging Center<br>
    123 Main Street<br>
    Anywhere, USA 01234<br>
    Phone 123.456.7890<br>
    Fax 123.456.7890
</p>

<p>
    <strong>PATIENT:</strong> {{ $report->patient_name}}<br>
    <strong>DOB:</strong>{{ $report->patient_dob}}<br>
    <strong>FILE #:</strong>{{ $report->scan_file}}<br>
    <strong>PHYSICIAN:</strong> {{ $report->user->username}}<br>
    <strong>EXAM:</strong>{{ $report->exam_id}}<br>
    <strong>DATE:</strong> {{ $report->scan_date}}
</p>

<!-- <div class="section-title">CLINICAL INFORMATION</div>
<p>History pancreatic cancer... Follow-up examination.</p> -->

<!-- <div class="section-title">Notes</div> -->
<p>{!! $report->notes !!}</p>

<p>[NationalRad Radiologist]<br>Board Certified Radiologist</p>
<br>
<p>THSI REPORT WAS ELECTRONICALLY SIGNED</p>

</body>
</html>
