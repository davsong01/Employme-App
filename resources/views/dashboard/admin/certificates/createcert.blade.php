@extends('dashboard.admin.index')
@section('css')
<style>
  /* The Modal (background) */
  .modal {
    display: none;
    /* Hidden by default */
    position: fixed;
    /* Stay in place */
    z-index: 1;
    /* Sit on top */
    padding-top: 100px;
    /* Location of the box */
    left: 0;
    top: 0;
    width: 100%;
    /* Full width */
    height: 100%;
    /* Full height */
    overflow: auto;
    /* Enable scroll if needed */
    background-color: rgb(0, 0, 0);
    /* Fallback color */
    background-color: rgba(0, 0, 0, 0.4);
    /* Black w/ opacity */
  }

  /* Modal Content */
  .modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 100%;
  }

  /* The Close Button */
  .close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    border-radius: 50%;
  }

  .close:hover,
  .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
  .modal-backdrop {
  position: relative;
  }
  a.pre-order-btn { 
    color:#000;
    background-color:gold;
    border-radius:1em;
    padding:1em;
    display: block;
    margin: 2em auto;
    width:100%;
    font-size:1.25em;
    font-weight:6600;
    text-align: center
  }

a.pre-order-btn:hover { 
    background-color:#000;
    text-decoration:none;
    color:gold;
}

</style>
@endsection
@section('title', 'Add Certificate')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        @include('layouts.partials.alerts')
                        <h4 class="card-title">Add new Certificate in {{$p_name}}</h4>
                        @if(isset($certificate_settings['auto_certificate_status']) && $certificate_settings['auto_certificate_status'] == 'yes')
                        {{-- <a href="{{route('certificates.generate', $p_id )}}" onclick="return(confirm('Are you sure'))" class="btn btn-info">Auto Generate Certificates (40/batch)</a> --}}
                        <a href="javascript:void(0)" class="btn btn-info" data-toggle="modal" data-target="#batchModal">Auto Generate Certificates</a>

                        <a href="{{ route('certificate.clear.duplicates', $p_id)}}" class="btn btn-danger">Clear Duplicates</a>
                        @endif
                    </div>
                    <form action="{{ route('certificates.save') }}" method="POST" enctype="multipart/form-data"
                        class="pb-2">
                        {{ csrf_field() }}
                        <!--Gives the first error for input name-->

                        <div><small>{{ $errors->first('title')}}</small></div>
                        <div class="form-group">

                            <label for="class">Select User *</label>

                            <select name="user_id" id="user_id" class="form-control" required>
                                <option value=""></option>
                                @foreach ($users->sortBy('name') as $user)
                                    @if($user->certificates_count <= 0)
                                        <option value="{{ $user->user_id }}">{{$user->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <div><small style="color:red">{{ $errors->first('user_id')}}</small></div>

                            <div class="form-group">
                                <label>Choose Certificate</label>
                                <input type="file" id="certificate" name="certificate" class="form-control" required>
                            </div>
                            <div><small style="color:red">{{ $errors->first('certificate')}}</small></div>
                        </div>
                        <input type="hidden" value="{{ $p_id }}" name="p_id">
                       
                        <button type="submit" class="btn btn-primary" style="width:100%">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="col-md-12">
            <div class="card-body">
                <table id="zero_config" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="text-align:center">
                                <a class="btn btn-warning btn-sm m-2" id="send-all">
                                    Actions
                                </a> <br>
                                <input type="checkbox" id="all"/>
                            </th>
                            <th>#</th>
                            <th>Preview</th> <!-- New Column for Preview -->
                            <th>Name</th>
                            @if(!empty($score_settings))
                            <th style="width: 115px;">Program Details</th>
                            @endif
                            <th>Details</th>
                            <th>Date Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <?php 
                            $results = $certificate->scores();
                        ?>
                        <tr>
                            <td style="width:2px;text-align:center;">
                                <input style="margin-right: 10px;" class="form-check-input downloads download-check" type="checkbox" value="{{$certificate->user_id}}">
                            </td>
                            <td>{{ $i++ }}</td>
                            <td style="text-align:center;">
                                @if($certificate->file)
                                    {{-- <a class="btn btn-info btn-sm" href="#" 
                                    onclick="loadCertificateImage({{ $certificate->id }}, '/certificate/{{ $certificate->file }}')">
                                        Preview
                                    </a> --}}
                                    <a class="btn btn-info btn-sm" href="#" onclick="loadCertificateImage(event, {{ $certificate->id }}, '/certificate/{{ $certificate->file }}')">Preview
                                    </a>
                                @else
                                    <span>No Preview Available</span>
                                @endif
                            </td>
                            <td>{{ isset($certificate->user->name) ? $certificate->user->name : 'N/A' }} <br>
                                <span style="font-style: italic">{{ $certificate->user->email }}</span> <br>
                                <span style="font-style: bold"> <strong>{{ $certificate->user->staffID }}</strong></span> <br>
                            </td>
                            @if(isset($score_settings) && !empty($score_settings))
                            <td style="width: 115px;">
                                @if(isset($score_settings->certification) && $score_settings->certification > 0)
                                    <strong>Certification: </strong> {{ isset($results['certification_test_score'] ) ? $results['certification_test_score'] : '' }}% 
                                @endif
                                @if(isset($score_settings->class_test) && $score_settings->class_test > 0)
                                    <strong class="tit">Class Tests:</strong> {{ isset($results['class_test_score'] ) ? $results['class_test_score'] : '' }}% <br>
                                @endif
                                @if(isset($score_settings->role_play) && $score_settings->role_play > 0)
                                    <strong class="tit">Role Play: </strong>{{ isset($results['role_play_score'] ) ? $results['role_play_score'] : '' }}% <br> 
                                @endif
                                @if(isset($score_settings->email) && $score_settings->email > 0)
                                    <strong>Email: </strong>{{ isset($results['email_test_score'] ) ? $results['email_test_score'] : '' }}%
                                @endif
                                
                                {{-- <strong class="tit" style="color:blue">Passmark</strong>{{ $score_settings->passmark }}% <br> --}}
                                <br>
                                <strong class="tit" style="color:{{ $results['total'] < $score_settings->passmark ? 'red' : 'green'}}"> Total: {{ $results['total'] }}%</strong> 
                            </td>
                            @endif
                            <td style="color:{{ $certificate->show_certificate() == 'Disabled' ? 'red' : 'green'}}">
                                Certificate Status: <strong>{{ $certificate->show_certificate() }}</strong>
                                @if($certificate->certificate_number)
                                <br>Certificate No: <strong>{{ $certificate->certificate_number }}</strong> <br>
                                <div class="form-group mb-3">
                                    <button id="copy-btn{{$certificate->id}}" class="btn btn-primary">
                                        <i class="fa fa-copy"></i> Copy Certificate Verification Link
                                    </button>
                                    <small id="copy-status{{$certificate->id}}" style="color: green; display: none;"></small>
                                </div>
                                <input type="text" id="verification-link{{$certificate->id}}" value="{{url('/api/verify-certificate').'?certificate_number='.$certificate->certificate_number }}" hidden>
                                <script>
                                    $('#copy-btn{{$certificate->id}}').click(function() {
                                        var verificationLink = $('#verification-link{{$certificate->id}}').val();
                                        
                                        var tempInput = $('<input>');
                                        $('body').append(tempInput);
                                        tempInput.val(verificationLink).select();
                                        document.execCommand("copy");
                                        tempInput.remove(); 

                                        $('#copy-status{{$certificate->id}}').text("{{$certificate->certificate_number}} Copied");
                                        $('#copy-status{{$certificate->id}}').fadeIn().delay(2000).fadeOut();
                                    });
                                </script>
                                @endif
                            </td>
                            <td>{{ $certificate->updated_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    @if($certificate->show_certificate() == 'Disabled')
                                    <a data-toggle="tooltip" data-placement="top" title="Enable certificate"
                                        class="btn btn-light" href="{{route('certificate.status', ['program_id'=>$certificate->program_id, 'user_id'=> $certificate->user_id, 'status'=>1, 'certificate_id' => $certificate->id]) }}"><i
                                            class="fa fa-toggle-on"></i>
                                    </a>
                                    @else
                                    <a data-toggle="tooltip" data-placement="top" title="Disable certificate"
                                        class="btn btn-light" href="{{route('certificate.status', ['program_id'=>$certificate->program_id, 'user_id'=> $certificate->user_id, 'status'=>0, 'certificate_id' => $certificate->id ]) }}"><i
                                            class="fa fa-toggle-off"></i>
                                    </a>
                                    @endif
                                    <a data-toggle="tooltip" data-placement="top" title="Download certificate"
                                        class="btn btn-info" href="/certificate/{{ $certificate->file }}"><i
                                            class="fa fa-download"></i>
                                    </a>
                                    
                                    <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Are you really sure?');">
                                        {{ csrf_field() }}
                                        {{method_field('DELETE')}}

                                        <button type="submit" class="btn btn-danger btn-xsm" data-toggle="tooltip"
                                            data-placement="top" title="Delete certificate"> <i
                                                class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="myModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select Action</h5>
                <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" style="padding: 10px 0;">
                    <select class="form-control" id="action" name="action" required>
                        <option value="" selected>Select Option</option>
                        <option value="enable" selected>Enable</option>
                        <option value="disable" selected>Disable</option>
                        @if(isset($certificate_settings['auto_certificate_status']) && $certificate_settings['auto_certificate_status'] == 'yes')
                        <option value="regenerate-certificate" selected>Regenerate Certificate</option>
                        @endif
                        <option value="delete-certificate" selected>Delete Certificate</option>
                    </select>
                </div>
                
                <input type="hidden" name="program_id" id="program_id" value="{{ $p_id }}">
                <div class="col-md-12" style="padding: 0px;">
                    <button id="promote-all" class="btn btn-icon btn-primary form-control"><span id="promote-phrase">Send</span> <span><i id="spinner" class="fa fa-spinner fa-spin" style="display:none"></i></span></button>
                </div>
            </div>
            
            <div class="modal-footer">
                <button id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="batchModal" tabindex="-1" aria-labelledby="exportmodal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="batchModalLabel">Auto Certificate Options</h5>
            <button type="button" class="close btn btn-danger" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form action="{{ route('certificates.generate', $p_id) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label for="batch-size" class="form-label">Batch Size</label>
                    <input type="number" class="form-control" id="batch-size" name="pick" min="1" value="50" required>
                </div>
                <div class="mb-3">
                    <label for="show_certificate" class="form-label">Enable Generated Certificates</label>
                    <select name="show_certificate" class="form-control" id="">
                        <option value="">Select</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="use_cron" class="form-label">Use Cron</label>
                    <select name="use_cron" class="form-control" id="">
                        <option value="">Select</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="generate-button">
                <span id="generate-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                Generate
            </button>
            </div>
        </form>
        </div>
    </div>
</div>

<div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body" style="text-align:center;">
                <div id="spinner" class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <img id="certificate-img" src="" alt="Certificate" style="display:none; width:500px; height:auto;">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#user_id').select2();
    });

    function loadCertificateImage(event,certificateId, filePath) {
        event.preventDefault();
        var modalId = '#certificateModal';
        var imageId = 'certificate-img';
        var spinnerId = 'spinner';

        var noCache = new Date().getTime(); 

        $('#certificate-img').hide();
        $('#spinner').show();

        var imageUrl = filePath + '?nocache=' + noCache;

        $('#' + imageId).attr('src', imageUrl).on('load', function () {
            $('#' + spinnerId).hide();
            $(this).show();
        });

        $(modalId).modal('show');
    }

</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.close').click(function(e){
            $("#myModal").modal('hide');
        });

        $('#all').click(function(e){
            if($(this).is(':checked')){
                $('.download-check').prop('checked', true);
                $('table .checker span').addClass('checked');
            }else{
                $('.download-check').prop('checked', false);
                $('table .checker span').removeClass('checked');
            }
        });

        $('#send-all').click(function(e){
            $("#myModal").modal('show');
        });

        $('#promote-all').click(function(e){
            var program_id =  $('#program_id').val();
            var action =  $('#action').val();
            var valuex = [];
            
            $('.downloads:checked').map(function(i, e){
                valuex.push($(e).val());
            });

            if(valuex.length > 0){
                callAjax(program_id, valuex, action);
            }else{
                alert("Please check one or more certificates to enable or disable");
            }
        });

        function callAjax(program_id,valuex,action){
            $.ajax({
            url: "{{ route('certificates.modify') }}",
            type: "POST",
            data: {
                program_id: program_id,
                action: action,
                data: valuex,
            },
            beforeSend: function(xhr){
                $('#spinner').show();
                $('#promote-phrase').hide();
            },
            success: function(res){
                $("#myModal").modal('hide');
                $('.download-check').prop('checked', false);

                location.reload();

                alert('Action performed successfully')
            }
        });

        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        const generateButton = document.getElementById('generate-button');
        const spinner = document.getElementById('generate-spinner');

        form.addEventListener('submit', function () {
            // Disable the button and show the spinner when the form is submitted
            generateButton.disabled = true;
            spinner.classList.remove('d-none'); // Show the spinner
        });
    });
</script>
@endsection