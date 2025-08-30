@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address-edit.css') }}">
@endsection

@section('content')
<div class="address-edit-container">
    <h2 class="address-edit-title">住所の変更</h2>
    <form action="{{ route('address.update') }}" method="POST" class="address-edit-form">
        @csrf
        <div class="form-group">
            <label for="zipcode">郵便番号</label>
            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $user->zipcode) }}">
            @error('zipcode') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}">
            @error('address') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->building ?? '') }}">
            @error('building') <div class="form-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="address-edit-btn">更新する</button>
    </form>
</div>
@endsection