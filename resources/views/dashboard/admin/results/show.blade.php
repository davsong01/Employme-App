<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Result - {{ $details->user->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #333;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }

        /* --- ENHANCED LOGO WATERMARK --- */
        .watermark {
            position: absolute;
            top: 50%;
            left: 63%;
            transform: translate(-50%, -50%);
            width: 500px; /* Increased size */
            height: 500px;
            opacity: 0.05; /* Keeps it professional and readable */
            z-index: 0;
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .watermark img {
            width: 100%;
            height: auto;
            /* Optional: filter: grayscale(100%); if you want it purely grey */
        }

        /* Container sized for A4 */
        .result-container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 15mm;
            box-sizing: border-box;
            border-top: 6px solid #28a745;
            position: relative;
            z-index: 1; /* Content stays above watermark */
            overflow: hidden; /* Ensures no spillover */
        }

        /* Header */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #eee;
            margin-bottom: 20px;
        }

        .logo-top img { max-height: 50px; }
        .title-text { color: #28a745; font-size: 20px; font-weight: 700; text-align: right; margin: 0; }

        /* Info Section */
        .info-table { width: 100%; margin-bottom: 25px; }
        .info-label { font-size: 11px; text-transform: uppercase; color: #888; font-weight: 600; }
        .info-value { font-size: 14px; font-weight: 700; margin-top: 2px; }

        /* Main Data Table */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; background: transparent; }
        .data-table thead th {
            background-color: rgba(248, 249, 250, 0.8);
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #dee2e6;
            font-size: 11px;
            color: #555;
        }
        .data-table tbody td { padding: 10px; border-bottom: 1px solid #eee; font-size: 13px; }

        /* Summary Section */
        .summary-table { width: 260px; float: right; margin-top: 10px; }
        .summary-table td { padding: 4px 0; font-size: 13px; }
        .total-row { color: #28a745; font-size: 16px; font-weight: 700; }
        
        .status-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
        }
        .certified { background: #d4edda; color: #155724; }
        .failed { background: #f8d7da; color: #721c24; }

        .clearfix::after { content: ""; clear: both; display: table; }

        /* Footer */
        .footer {
            position: absolute;
            bottom: 15mm;
            width: calc(100% - 30mm);
            text-align: center;
        }
        .signature img { width: 120px; }
        .disclaimer { font-size: 9px; color: #aaa; margin-top: 15px; text-transform: uppercase; }

        .actions { position: fixed; top: 20px; right: 20px; z-index: 100; }
        .btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; }

        @media print {
            .actions { display: none !important; }
            body { background: white; }
            .result-container { margin: 0; box-shadow: none; height: 297mm; }
        }
    </style>
</head>
<body>

    <div class="actions">
        <a href="javascript:window.print();" class="btn">PRINT RESULT</a>
    </div>

    <div class="result-container">
        
        <!-- Large Background Watermark -->
        <div class="watermark">
            <img src="{{ asset('assets/images/logo-text.png') }}" alt="Watermark">
        </div>

        <table class="header-table">
            <tr>
                <td class="logo-top"><img src="{{ asset('assets/images/logo-text.png') }}" alt="Logo"></td>
                <td><p class="title-text">STATEMENT OF RESULT</p></td>
            </tr>
        </table>

        <table class="info-table">
            <tr>
                <td width="55%">
                    <div class="info-label">Candidate Name</div>
                    <div class="info-value">{{ strtoupper($details->user->name) }}</div>
                    
                    @if(!empty($details->user->staffID))
                        <div class="info-label" style="margin-top:10px">Internal Record</div>
                        <div class="info-value" style="color:#007bff">Staff ID: {{ $details->user->staffID }}</div>
                    @endif
                </td>
                <td width="45%" style="text-align: right;">
                    <div class="info-label">Training Program</div>
                    <div class="info-value">{{ strtoupper($details->program->p_name) }}</div>
                    
                    <div class="info-label" style="margin-top:10px">Issue Date</div>
                    <div class="info-value">{{ date('d F, Y') }}</div>
                </td>
            </tr>
        </table>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Assessment Component</th>
                    <th style="text-align: right;">Score</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($program->scoresettings->class_test) && $program->scoresettings->class_test > 0)
                <tr>
                    <td>Class Test Assessment</td>
                    <td style="text-align: right;">{{ $details->class_test_score }}</td>
                </tr>
                @endif
                @if(!empty($program->scoresettings->email) && $program->scoresettings->email > 0)
                <tr>
                    <td>Professional Email Test</td>
                    <td style="text-align: right;">{{ $details->email_test_score }}</td>
                </tr>
                @endif
                @if(!empty($program->scoresettings->role_play) && $program->scoresettings->role_play > 0)
                <tr>
                    <td>Practical Role Play Evaluation</td>
                    <td style="text-align: right;">{{ $details->roleplay_test_score }}</td>
                </tr>
                @endif
                @if(!empty($program->scoresettings->crm_test) && $program->scoresettings->crm_test > 0)
                <tr>
                    <td>CRM Systems Proficiency</td>
                    <td style="text-align: right;">{{ $details->crm_test_score }}</td>
                </tr>
                @endif
                @if(!empty($program->scoresettings->certification) && $program->scoresettings->certification > 0)
                <tr>
                    <td>Final Certification Exam</td>
                    <td style="text-align: right;">{{ $details->certification_test_score }}</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="clearfix">
            <table class="summary-table">
                <tr>
                    <td style="color:#888">Total Obtainable Points</td>
                    <td style="text-align: right; font-weight:700">100</td>
                </tr>
                <tr>
                    <td style="color:#888">Pass Mark Threshold</td>
                    <td style="text-align: right; font-weight:700">{{ $details->scoresettings->passmark }}</td>
                </tr>
                <tr class="total-row">
                    <td>Final Score Achieved</td>
                    <td style="text-align: right;">{{ $details->total_score }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right; padding-top: 15px;">
                        <span class="status-pill {{ $details->certification_status == 'CERTIFIED' ? 'certified' : 'failed' }}">
                            {{ $details->certification_status }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="signature">
                <img src="{{ asset('assets/inc/sign.png') }}" alt="Signature">
                <div style="width: 160px; border-top: 1px solid #333; margin: 0 auto 5px auto;"></div>
                <div style="font-size: 12px; font-weight: 700;">School Administrator</div>
            </div>

            <div style="margin-top: 15px; font-size: 10px; color: #666;">
                REF: <strong>{{ strtoupper($details->program->p_abbr) }}/RES/{{ $details->user->id }}/{{ date('y') }}</strong>
            </div>

            <p class="disclaimer">
                Any alteration whatsoever renders this result invalid. <br>
                Verification can be performed via the portal using the reference number above.
            </p>
        </div>
    </div>

</body>
</html>