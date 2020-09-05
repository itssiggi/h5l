@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-10">
    @each('events.partials._banner', $events, 'event')
</div>
@endsection
