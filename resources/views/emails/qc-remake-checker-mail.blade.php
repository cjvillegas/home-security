@component('mail::message')

<h2>Good day! QC Remake Checker has been made by {{ $qcRemake->user->name }}.
<br>
Customer: {{ $qcRemake->order->customer }}
<br>
OrderNo. {{ $qcRemake->order_no }}
<br>
Report number: {{ $qcRemake->report_no }}</h2>
<hr>

@foreach ($qcRemake->validatedBlinds as $validatedBlind)

<table class="table mt-2">
    <thead>
        <tr>
            <th>Blind ID: {{ $validatedBlind->barcode }} </th>
        </tr>
    </thead>
    <tbody>
        @foreach($verifications as $verification)
        <tr <?php if (in_array($verification['key'], $validatedBlind->question_key)) {?> style="background-color: #66ff66;" <?php } else {  ?> style="background-color: #ff6666;" <?php }?>>
            <td>
                {{ $verification['value'] }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if (!is_null($validatedBlind->reason))
<br>
<p style="margin-top: 3px;">Reason: {{ $validatedBlind->reason }}</p>
@endif
<hr>
@endforeach
<br>

@endcomponent
