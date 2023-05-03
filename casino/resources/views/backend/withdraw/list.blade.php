@extends('backend.layouts.app')

@section('page-title', trans('app.pyour_withdraw'))
@section('page-heading', trans('app.pyour_withdraw'))

@section('content')

<section class="content-header">
    @include('backend.partials.messages')
</section>

<section class="content">
    <div class="tableList">
        <table class="table bg-white text-center">
            <thead>
                <tr>
                    <th scope="col">UserName</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Wallet</th>
                    <!-- <th scope="col">Shop</th> -->
                    <th scope="col">Status</th>
                    <th scope="col">Created At</th>
                    <th scope="col">Confirmed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($withdraws as $w)
                <tr class="fw-bold">
                    <td>{{ $w->user->username }}</td>
                    <td>{{ $w->amount }} {{ $w->currency }}</td>
                    <td>{{ $w->wallet }}</td>
                    <!-- <td>{{ $w->shop->name }}</td> -->
                    <td>{{ $w->status ? : 'Pending' }}</td>
                    <td>{{ $w->created_at }}</td>
                    <td>{{ $w->confirmed_at }}</td>
                </tr>
                @endforeach
                @if(count($withdraws) == 0)
                <tr>
                    <td colspan="10">
                        <div class="noData">
                            No diplay data.
                        </div>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</section>
@stop

@section('scripts')

@stop