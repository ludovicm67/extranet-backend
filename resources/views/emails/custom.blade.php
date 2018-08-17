@component('mail::message')
# {{ $title }}

{{ $content }}

Bonne journée,<br>
{{ config('app.name') }}
@endcomponent
