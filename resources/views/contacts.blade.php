@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <contacts :contacts="{{ $contacts }}" />
</div>
@endsection
