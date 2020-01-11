@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <contact :contact="{{ $contact }}" />
</div>
@endsection
