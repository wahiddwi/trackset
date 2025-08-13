@component('mail::message')
# {{$details['title']}}

{{$details['body']}}<br>
<table>
    <tr>
        <td>ID</td><td>:</td><td>{{$details['id']}}</td>
    </tr>
    <tr>
        <td>Password</td><td>:</td><td>{{$details['pass']}}</td>
    </tr>
</table>

@component('mail::button', ['url' => $details['url']])
RG Portal
@endcomponent

Segera ubah password anda setelah login.<br>

Terima Kasih,<br>
{{ config('app.name') }}
@endcomponent