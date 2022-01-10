@extends('layouts.export')
@section('content')
    <div>
        <div class="picking-information">
            <div class="d-inline-block">
                <h3>Order Picking List</h3>
                @isset($barcode)
                    @php
                        echo $barcode;
                    @endphp
                @endisset
            </div>

            <div class="d-inline-block ml-5">
                <p>
                    <b>Order Number: {{ $stockOrder->sage_order_no }}</b>
                </p>
                <p>
                    <b>Internal Order Number: {{ $stockOrder->order_no }}</b>
                </p>
                <p>
                    <b>Order Date: {{ $stockOrder->formatColumnDate('created_at') }}</b>
                </p>
            </div>

            <div class="d-inline-block float-r">
                <p>
                    Requested By: {{ $stockOrder->creator->name ?? 'N/A' }}
                </p>
                <p>
                    Approved By: {{ $stockOrder->approver->name ?? 'N/A' }}
                </p>
                <p>
                    Approved Date: {{ $stockOrder->formatColumnDate('approved_at') }}
                </p>
            </div>
        </div>

        <div class="picking-list-main-content mt-5">
            <table>
                <thead>
                <tr>
                    <th>Picked By</th>
                    <th>Date Picked</th>
                    <th>Packed By</th>
                    <th>Checked By</th>
                    <th>Instructions By</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                </tbody>
            </table>

            <table class="mt-5">
                <colgroup>
                    <col span="1">
                    <col span="1">
                    <col span="1">
                    <col span="1">
                    <col span="1">
                    <col span="1">
                </colgroup>

                <thead>
                    <tr>
                        <th style="width: 10%">Bin(Location)</th>
                        <th style="width: 20%">Item Code</th>
                        <th style="width: 40%">Description</th>
                        <th style="width: 10%">Qty Required</th>
                        <th style="width: 10%">Qty Picked</th>
                        <th style="width: 10%">In Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->BinLocation ?? 'N/A' }}</td>
                            <td>{{ $item->ItemCode ?? 'N/A' }}</td>
                            <td>{{ $item->Description ?? 'N/A' }}</td>
                            <td class="text-right">{{ $item->LineQuantity ?? 'N/A' }}</td>
                            <td>&nbsp;</td>
                            <td class="text-right">{{ $item->QtyInStock ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
