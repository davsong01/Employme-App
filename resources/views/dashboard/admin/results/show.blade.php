<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>
        Result
    </title>
    <style type="text/css">
        .fil {
            background-color: #9F9;
            color: #000;
        }

        .fil2 {
            background-color: #FF6;
            color: #000;
        }

        td {
            border: solid 1px
        }

        table {
            width: 100%;
        }

        .style1 {
            border-style: none;
            border-color: inherit;
            border-width: medium;
            width: 100%;
            height: 100px;
        }

        .style2 {
            font-weight: bold;
            border: NONE;
        }

        td.datacellone {
            border: NONE;
            background-color: #FFC;
            color: black;
        }

        td.datacelltwo {
            border: NONE;
            background-color: #fff;
            color: black;
        }

        .style3 {
            width: 72%;
        }
    </style>
</head>

<body>

    <script type="text/javascript">
        //<![CDATA[
        var theForm = document.forms['form1'];
        if (!theForm) {
            theForm = document.form1;
        }

        function __doPostBack(eventTarget, eventArgument) {
            if (!theForm.onsubmit || (theForm.onsubmit() != false)) {
                theForm.__EVENTTARGET.value = eventTarget;
                theForm.__EVENTARGUMENT.value = eventArgument;
                theForm.submit();
            }
        }
        //]]>
    </script>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                    <div align="center">
                            <div><img src="{{ asset('assets/images/logo-text.png') }}" />
                                <h2 style="color:green">STATEMENT OF RESULT</h2>
                            </div>
                            <div style="text-align:left; height:auto; width:900px">
                                <div style="text-align:center; font-weight:bold; width:100%; font-size: large;"></div><br />
                                <span id="LblName" style="font-size:Small;font-weight:bold;">{{strtoupper($details['name'])}}</span>
                                <br />
                                <span id="LblMatricno2"
                                    {{-- style="font-size:Small;font-weight:bold;">R/{{strtoupper($details->program->p_abbr)}}/{{strtoupper($details->program_id)}}/{{strtoupper($details->user->id)}}</span> --}}
                                <br />
                                <span id="LblDept" style="font-size:Small;font-weight:bold;">{{strtoupper($details['program'] )}}</span>
                                <br />
                                <hr />
                    
                    
                            </div>
                            <div style="width:900px; border:solid 1px; height :auto">
                                <span id="Lblcontent1">
                                    <table cellpadding='3' cellspacing='2'>
                                        <tr style='background-color:#9F9; color:#000; border:1px solid #000; font-weight:bold;'>
                                            <td>ACTIVITY</td>
                                            <td>POINT</td>
                                        </tr>
                                        <tr>
                                            <td class="datacellone">Class Test</td>
                                            <td class="datacellone">{{ $details['class_test_score'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="datacelltwo">Email Test</td>
                                            <td class="datacelltwo">{{ $details['email_test_score'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="datacellone">Role Play</td>
                                            <td class="datacellone">{{ $details['role_play_score'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="datacelltwo">Certification Test</td>
                                            <td class="datacelltwo">{{ $details['certification_test_score'] }}</td>
                                        </tr>
                                    </table>
                                </span>
                            </div>
                            <hr />
                            <div style="width:900px; height :400px">
                                <table class="style1">
                                    <tr>
                                        <td style="text-align:left; width:50%  " valign="top" class="style2">
                                            <br />
                                            <table cellpadding="5" cellspacing="2">
                                                <tr>
                                                    <td class="fil">
                                                        Points Obtainable</td>
                                                    <td>
                                                        <span id="lblUnit1">100</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fil">
                                                        Pass Mark</td>
                                                    <td>
                                                        <span id="LblPoint1">{{ $details['passmark'] }}</span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fil">
                                                        Points Scored</td>
                                                    <td>
                                                        <span id="Lblgpa1">{{ $details['total_score'] }}</span>
                                                    </td>
                                                </tr>
                                            </table>
                                       </td>
                                    </tr>
                                </table>
                                <div style="text-align:left">
                                    <span style="">CERTIFICATION STATUS : </span>
                                    <span id="lblRemark" style="color:{{ $details['status'] == 'CERTIFIED' ? 'green' : 'red'}}"><b>{{ $details['status'] }}</b></span>
                                    <br />
                                    <br />
                                    <div style="width:100%; text-align:center"><br /><br />
                                        <img src="{{ asset('assets/inc/sign.png') }}" style="width:8%" /><br />
                                        .................................................................<br />
                                        School Administrator
                                        <br /><br /><br />ANY ALTERATION WHATSOVER RENDERS THIS RESULT INVALID<br />
                                        <br /><span>Printed: {{now()}}</span><br /><br /><br />
                                        <a id="lnkclose" href="/training/{{ $program->id }}">BACK</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        <a onclick="javascript:window.print();" id="LinkButton1"
                                            href="javascript:__doPostBack(&#39;LinkButton1&#39;,&#39;&#39;)">PRINT</a>
                                    </div>
                                </div>
                            </div>
                    
                        </div>
            </div>
        </div>
    </div>
</body>

</html>