<?php
use App\Models\Settings;
use App\Models\Program;
use App\Models\Group;

$setting = Settings::first();
$logo = $setting->logo;
$currency_symbol = $setting->CURR_ABBREVIATION;
$trainings = Program::mainActivePrograms()->latest()->take(4)->get();

$packages = Group::with(['programs' => function ($q) {
    $q->mainActivePrograms(); 
}])
->isActive()
->latest()
->take(4)
->get();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
</head>
<body style="margin:0; padding:0; width:100%; background:#f2f4f6; font-family:Arial,Helvetica,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation">
                    {{-- ───── HEADER (centered logo) ───── --}}
                    <tr>
                        <td style="padding:25px 0; text-align:center;">
                            <a href="{{url('/')}}"><img src="{{ url('logo') }}" alt="{{ config('app.name') }}" style="max-height:60px; border:0;"></a>
                        </td>
                    </tr>

                    {{-- ───── BODY: injected MarkDown / Blade content ───── --}}
                    <tr>
                        <td style="background:#ffffff; padding:35px; border-top:1px solid #eaeaea;">
                            {!! Illuminate\Mail\Markdown::parse($slot) !!}
                        </td>
                    </tr>

                    {{-- ───── TOP FOOTER: Latest Programs Grid ───── --}}
                    @if($trainings->count())
                        <tr>
                            <td style="background:#ffffff; padding:25px 35px; border-top:1px solid #eaeaea;">
                                <h2 style="margin:0 0 15px; font-size:18px; color:#333;">
                                    Latest Programmes
                                </h2>

                                {{-- 2×2 grid built with a table for email safety --}}
                                <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-top: 20px;">
                                    @foreach($trainings->chunk(2) as $row)
                                        <tr>
                                            @foreach($row as $training)
                                                <td width="50%" style="padding: 10px; vertical-align: top;">
                                                    <table cellpadding="0" cellspacing="0" role="presentation" width="100%" style="border:1px solid #ddd; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); overflow: hidden;">
                                                        <tr>
                                                            <td style="padding: 0; text-align: center;">
                                                                <a href="{{ route('trainings', $training->slug) }}" target="_blank">
                                                                    <img src="{{ url($training->image) }}" alt="{{ $training->p_name }}"
                                                                        style="width: 100%; max-height: 250px; object-fit: cover; display: block;">
                                                                </a>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding: 10px 12px;">
                                                                <a href="{{ route('trainings', $training->slug) }}" target="_blank"
                                                                    style="text-decoration: none; color: #333; font-size: 14px; font-weight: bold; display: block;">
                                                                    {{ $training->p_name }}
                                                                </a>
                                                                <span style="color: #888; font-size: 13px;">
                                                                    {{ $currency_symbol }}{{ number_format($training->p_amount) }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            @endforeach
                                
                                            {{-- Fill empty cell if only one item in this row --}}
                                            @if($row->count() < 2)
                                                <td width="50%" style="padding: 10px;"></td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </table>
                                
                                
                            </td>
                        </tr>
                    @endif

                    {{-- ───── BOTTOM FOOTER (default) ───── --}}
                    <tr>
                        <td style="background:#ffffff; padding:25px 35px; border-top:1px solid #eaeaea; text-align:center; color:#555;">
                            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.<br>
                            <small style="color:#999;">
                                {{ config('app.url') }}
                            </small>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
