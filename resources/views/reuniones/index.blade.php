@extends('layouts.app')

@section('title')
    Reuniones
@endsection

@section('content')

<div class="row">
    <div class="col-sm-12">
        @include('flash-message')
    </div>
</div>

@can('create', App\Reunion::class)
    <div class="row">
        <div class="col-md-12 py-2">
        <a class="btn btn-primary" href="{{route('reuniones.create')}}" role="button">
                <i class="fa fa-calendar-plus" aria-hidden="true"></i>
                <span class="ml-2">Agendar reunión</span>
            </a>
        </div>
    </div>
    <hr>
@endcan

@if ($reuniones->isNotEmpty())

<div class="row mb-2">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h4>Próximas reuniones</h4>
    </div>
    @foreach ($reuniones as $reunion)
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card reunion-convocado my-2">
            <div class="card-body">
                @php
                    $inicio = Carbon\Carbon::parse($reunion->inicio);
                    $fin = Carbon\Carbon::parse($reunion->fin);
                @endphp
                <p class="card-text"><strong>{{$inicio->format('d/m/Y')}}</strong></p>
                <p class="card-text">Inicia: {{$inicio->format('h:i A')}}</p>
                <p class="card-text">Finaliza: {{$fin->format('h:i A')}}</p>
                <p class="card-text">Lugar: {{$reunion->lugar}} </p>
                <p class="card-text">Academia: {{$reunion->academia->nombre}}</p>
                <p class="card-text">
                    <a name="" id="ver-reunion-{{$reunion->id}}" class="btn btn-success" href="{{route('reuniones.show', $reunion->id)}}" role="button">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <span class="ml-1">Ver detalles</span>
                    </a>
                </p>
                @if ($reunion->orden_del_dia)
                <p class="card-text">
                    <a name="" id="descargar-od-{{$reunion->id}}" class="btn btn-danger" href="{{route('reuniones.ordendeldia.descargar', $reunion->id)}}" role="button" target="__blank">
                        <i class="fas fa-file-pdf"></i>
                        <span class="ml-1">Orden del Día</span>
                    </a>
                </p>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="row">
    <div class="col-sm-12">
        {{$reuniones->links()}}
    </div>
</div>

@else

@endif
@endsection
{{-- @push('estilos')
<style>
    
</style>
@endpush --}}
