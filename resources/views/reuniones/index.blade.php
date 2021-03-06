@extends('layouts.app')

@section('title')
    Reuniones
@endsection

@section('page-scripts')
<script src="{{asset('js/reuniones/index.js')}}"></script>
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
        <seleccionar-academia-modal></seleccionar-academia-modal>        
    </div>
</div>
<hr>
@endcan

{{-- Filtrado de reuniones --}}
<div class="row py-2">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="card">
            <div class="card-body" style="background-color: #ececec">
                <h5 class="card-title">Filtrar reuniones</h5> 
                <form action="{{url('/reuniones')}}" method="get" class="container">
                    <div class="form-row">
                        <div class="form-group col-sm-12 col-md-4">
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($minuta == "con")
                                        <input type="radio" class="form-check-input" name="minuta" id="check-con-minuta" value="con" checked>
                                    @else
                                        <input type="radio" class="form-check-input" name="minuta" id="check-con-minuta" value="con">
                                    @endif
                                    Reuniones con minuta realizada
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($minuta == "sin")
                                        <input type="radio" class="form-check-input" name="minuta" id="check-sin-minuta" value="sin" checked>
                                    @else
                                        <input type="radio" class="form-check-input" name="minuta" id="check-sin-minuta" value="sin">
                                    @endif
                                    Reuniones sin minuta
                                </label>
                            </div>
                            <div class="form-check">
                                <label class="form-check-label">
                                    @if ($minuta == "todas" || $minuta == null)
                                        <input type="radio" class="form-check-input" name="minuta" id="check-todas" value="todas" checked>
                                    @else
                                        <input type="radio" class="form-check-input" name="minuta" id="check-todas" value="todas">
                                    @endif
                                    Todas
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <label for="despuesde">Despues de</label>
                            <input type="date" class="form-control" name="despuesde" id="despuesde" value="{{$despuesde ?: ""}}">
                        </div>
                        
                        <div class="form-group col-sm-12 col-md-4">
                            <label for="antesde">Antes de</label>
                            <input type="date" class="form-control" name="antesde" id="antesde" value="{{$antesde ?: ""}}">
                        </div>

                        <div class="form-group col-sm-12 col-md-4">
                            <button type="submit" class="btn btn-secondary">Aplicar Filtros</button>
                            <a name="btn-index-reuniones" id="btn-index-reuniones" class="btn btn-primary" href="{{route('reuniones.index')}}" role="button">
                                Limpiar filtros
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($reuniones->isNotEmpty())
<div class="row py-2">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h4>Reuniones</h4>
    </div>
    @foreach ($reuniones as $reunion)
    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        <div class="card reunion-convocado my-2">
            <div class="card-body">
                <p class="card-text"><strong>{{formato_fecha_esp($reunion->inicio)}}</strong></p>
                <p class="card-text">Academia de {{$reunion->academia->nombre}}</p>
                <p class="card-text">Inicia: {{$reunion->inicio->format('h:i A')}}</p>
                <p class="card-text">Finaliza: {{$reunion->fin->format('h:i A')}}</p>
                <p class="card-text">Lugar: {{$reunion->lugar}} </p>
                <p class="card-text">
                    <a name="ver-reunion-{{$reunion->id}}" id="ver-reunion-{{$reunion->id}}" class="btn btn-success" href="{{route('reuniones.show', $reunion->id)}}" role="button">
                        <i class="fa fa-calendar" aria-hidden="true"></i>
                        <span class="ml-1">Ver detalles</span>
                    </a>
                </p>
                @if ($reunion->orden_del_dia)
                <p class="card-text">
                    <a name="descargar-od-{{$reunion->id}}" id="descargar-od-{{$reunion->id}}" class="btn btn-danger" href="{{route('reuniones.ordendeldia.descargar', $reunion->id)}}" role="button" target="__blank">
                        <i class="fas fa-file-pdf"></i>
                        <span class="ml-1">Orden del Día</span>
                    </a>
                </p>
                @endif

                {{-- Condiciones para botones de minuta --}}
                @if ($reunion->minuta)
                    <a name="descargar-minuta-{{$reunion->id}}" id="descargar-minuta-{{$reunion->id}}" class="btn btn-danger" href="{{route('reuniones.minuta.index', $reunion->id)}}" role="button" target="__blank">
                        <i class="fas fa-file-pdf"></i>
                        <span class="ml-1">Minuta</span>
                    </a>
                @else
                    @can('crearMinuta', $reunion)
                        <p class="card-text">
                            <a name="crear-minuta-{{$reunion->id}}" id="crear-minuta-{{$reunion->id}}" class="btn btn-primary" href="{{route('reuniones.minuta.create', $reunion->id)}}" role="button">
                                <i class="fas fa-file"></i>
                                <span class="ml-1">Crear minuta</span>
                            </a>
                        </p>
                    @endcan
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>
@else

@endif
@endsection
{{-- @push('estilos')
<style>
    
</style>
@endpush --}}
