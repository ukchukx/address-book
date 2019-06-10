@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <contact-view :contacts="{{ $contacts }}" />
</div>
@endsection
