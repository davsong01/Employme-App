@component('mail::message')
Dear {{ $name }},

{!! $data !!}

@endcomponent

