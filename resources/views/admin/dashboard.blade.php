@extends('layout')

@section('content')
    <div>
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-2xl font-bold text-gray-800">Thống Kê Nhà Sách</h2>
        </div>

        <x-infomation-section />
        <x-total-component />
        <div class="flex flex-col gap-5 md:flex-row">
            <div class="md:w-1/2 pt-5">
                <x-list-book-recent-order />
            </div>
            <div class="md:w-1/2 pt-5">
                <x-list-user-orders />
            </div>
        </div>
        <div class="pt-5">
            <x-recent-order-list />
        </div>
    </div>
@endsection
