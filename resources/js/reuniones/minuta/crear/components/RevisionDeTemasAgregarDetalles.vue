<template>
  <div class="my-2">
    <!-- ------ Formulario para agregar comentario ---------------  -->
    <b-form-group>
      <label :for="'textarea-comentario-'+temaId">
        Comentarios generales <span class="text-danger">*</span>
      </label>
      <b-form-textarea
        :id="'textarea-comentario-'+temaId"
        :name="'textarea-comentario-'+temaId"
        debounce="600"
        v-model="comentario"
        :state="comentario.length >= 10 && comentario.length <= 500"
        placeholder="Descripción breve de lo hablado en la reunión acerca de este tema..."
        @change="actualizarComentario()"
        rows="3"
        max-rows="8"
        trim
      ></b-form-textarea>
      <small class="text-muted form-text">{{comentario.length}}/500</small>
    </b-form-group>
    
    <!-- ------ Checkbox para mostrar el formulario de acuerdos ---------------  -->
    <b-form-group>
      <b-form-checkbox v-model="generarAcuerdos" name="check-button" switch>
        <b>¿Agregar acuerdos referentes a este tema?</b>
      </b-form-checkbox>
    </b-form-group>
    
    <!-- ------ Formulario para generar nuevos acuerdos ---------------  -->
    <div v-if="generarAcuerdos" class="container-fluid py-2">
      <div class="row form-group">
        <div class="col-sm-12">
          <b>Nuevo acuerdo</b>
        </div>
      </div>

      <!-- Input para Descripción del acuerdo  -->
      <div class="row form-group">
        <label
          :for="'descripcion-txt-'+temaId"
          class="col-sm-12 col-md-3 col-form-label text-md-right"
        >Acuerdo</label>
        <div class="col-sm-12 col-md-6">
          <b-form-input 
            :id="'descripcion-txt-'+temaId" 
            :name="'descripcion-txt-'+temaId" 
            v-model="nuevoAcuerdo.descripcion"
            trim
          ></b-form-input>
        </div>
      </div>
  
      <!-- Barra de búsqueda para responsable del acuerdo  -->
      <div class="row form-group" v-if="mostrarBusqueda">
        <label
          :for="'responsable-txt-'+temaId"
          class="col-sm-12 col-md-3 col-form-label text-md-right"
        >Responsable</label>
        <div class="col-sm-12 col-md-6">
          <autocomplete
            :search="buscarResponsable"
            :id="'responsable-search-'+temaId" 
            placeholder="Buscar usuario"
            aria-label="Buscar usuario"
            :get-result-value="obtenerNombreCompleto"
            @submit="procesarResponsable"
          >
            <template #result="{ result, props }">
              <li v-bind="props" class="autocomplete-result">{{result | nombreCompleto}}</li>
            </template>
          </autocomplete>
    
        </div>
      </div>

      <!-- Input para producto esperado del acuerdo  -->
      <div class="row form-group">
        <label
          :for="'producto-esperado-txt'+temaId"
          class="col-sm-12 col-md-3 col-form-label text-md-right"
        >Resultado/Producto esperado</label>
        <div class="col-sm-12 col-md-6">
          <b-form-input
            :id="'producto-esperado-txt'+temaId"
            :name="'producto-esperado-txt'+temaId"
            v-model="nuevoAcuerdo.producto_esperado"
            trim
          ></b-form-input>
        </div>
      </div>

      <!-- Input para fecha compromiso de resolución del acuerdo  -->
      <div class="row form-group">
        <label
          :for="'fecha-compromiso'+temaId"
          class="col-sm-12 col-md-3 col-form-label text-md-right"
        >Fecha compromiso de resolución</label>
        <div class="col-sm-12 col-md-6">
          <date-picker
            :id="'fecha-compromiso'+temaId"
            :name="'fecha-compromiso'+temaId"
            v-model="nuevoAcuerdo.fecha_compromiso" :config="optionsDatePicker"
          >
          </date-picker>
        </div>
      </div>

      <!-- Botón para agregar el acuerdo  -->
      <div class="form-group row mb-0">
        <div class="col-md-6 offset-md-3">
          <b-button variant="success" @click="agregarAcuerdo" :disabled="botonAgregarDeshabilitado">
            <i class="fa fa-plus mx-1" aria-hidden="true"></i> Agregar acuerdo
          </b-button>
        </div>
      </div>
    </div>

    <!-- ----------------- Lista de acuerdos generados ---------------  -->
    <div v-if="listaDeAcuerdos" class="container-fluid py-2">
      <b-row>
        <b-col 
          sm="6" md="4" lg="3"
          v-for="(acuerdo, index) in listaDeAcuerdos" 
          :key="acuerdo.uuid"
        >
          <div class="card reunion-convocado">
            <div class="card-header">
              <b>Acuerdo {{index+1}}</b>
              <button type="button" class="close" @click.prevent="quitarAcuerdo(acuerdo)">×</button>
            </div>
            <div class="card-body">
              <h5 class="card-title">{{acuerdo.descripcion}}</h5>
              <p class="card-text"><b>Fecha comprimiso de resolución:</b> {{acuerdo.fecha_compromiso}}</p>
              <p class="card-text"><b>Resultado/producto esperado:</b> {{acuerdo.producto_esperado}}</p>
              <p class="card-text"><b>Responsable:</b> {{acuerdo.responsable | nombreCompleto}}</p>
            </div>
          </div>
        </b-col>
      </b-row>
    </div>
  </div>
</template>

<script>
import {format, parseISO} from 'date-fns';
import { obtenerNombreCompleto } from '../../../../helpers';

import { mapActions, mapGetters } from 'vuex';

export default {
  props: ["temaId", "comentarioProp"],
  mounted() {
    // console.log()
  },
  data() {
    return {
      optionsDatePicker: {
        locale: "es",
        format: "DD/MM/YYYY",
        daysOfWeekDisabled: [0],
        minDate: moment(),
      },
      comentario: this.comentarioProp ? this.comentarioProp : "",
      generarAcuerdos: false,
      mostrarBusqueda: true,
      nuevoAcuerdo: {
        uuid: '',
        tema_id: this.temaId,
        responsable: null,
        descripcion: '',
        producto_esperado: '',
        fecha_compromiso: null,
      }
    };
  },
  methods: {
    ...mapActions(['ponerComentarioEnTema', 'ponerNuevoAcuerdo', 'quitarAcuerdo']),

    obtenerNombreCompleto,

    actualizarComentario() {
      this.ponerComentarioEnTema({
        temaId: this.temaId,
        comentario: this.comentario
      });
    },

    // ------ Método para agregar acuerdo a la lista de acuerdos en el estado -----------
    agregarAcuerdo() {
      // this.nuevoAcuerdo.uuid = uuidv4();
      let payload = { ...this.nuevoAcuerdo };
      this.ponerNuevoAcuerdo(payload);
      // Limpiar formulario
      this.nuevoAcuerdo.uuid = '';
      this.nuevoAcuerdo.descripcion = '';
      this.nuevoAcuerdo.responsable = null;
      this.nuevoAcuerdo.producto_esperado = '';
      this.nuevoAcuerdo.fecha_compromiso = '';
      // Un cambio entre mostrar limpia el formulario de búsqueda
      this.mostrarBusqueda = false;
      this.$nextTick(() => {
          this.mostrarBusqueda = true
      });
    },

    buscarResponsable(busqueda){
      // Los responsables pueden ser convocados a la reunión o el mismo presidente de la academia
      let resultado = [this.reunion.academia.presidente, ...this.reunion.convocados];
      if(busqueda){
        busqueda = busqueda.toLowerCase()
        // Busco el nombre del presidente (paso todo a minúsculas)
        let resultadoPresidente = obtenerNombreCompleto(this.reunion.academia.presidente)
                                      .toLowerCase()
                                      .includes(busqueda) 
                                      ? [this.reunion.academia.presidente] : [];

        // Busco el nombre en los convocados (paso todo a minúsculas)
        let resultadoConvocados = this.reunion.convocados.filter(
          convocado => obtenerNombreCompleto(convocado).toLowerCase().includes(busqueda)
        )
        resultado = [...resultadoPresidente, ...resultadoConvocados];
      }
      return resultado;
    },
    // Función llamada al presionar alguna de las opciones del autocompletado
    procesarResponsable(responsable){
      this.nuevoAcuerdo.responsable = responsable;
    },
    longitudDeCadenaValida(cadena){
      return cadena 
            ? (cadena.length > 3 && cadena.length < 191) 
            : false;
    }
  },
  computed: {
    ...mapGetters(['reunion', 'acuerdosPorTemaId']),
    botonAgregarDeshabilitado(){
      return !(this.nuevoAcuerdo.tema_id 
            && this.nuevoAcuerdo.responsable
            && this.longitudDeCadenaValida(this.nuevoAcuerdo.descripcion)
            && this.longitudDeCadenaValida(this.nuevoAcuerdo.producto_esperado)
            && this.nuevoAcuerdo.fecha_compromiso);
    },
    listaDeAcuerdos(){
      return this.acuerdosPorTemaId(this.temaId);
    }
  },
  filters:{
    nombreCompleto: function (usuario) {
      return usuario.id ? `${usuario.apellido_paterno} ${usuario.apellido_materno} ${usuario.nombre} ${usuario.grado}` : '';
    },
    fecha: function (ISOstring) {
      return moment.parse(ISOstring, 'dd/MM/y');
    },
  },
  watch: {
    
  },
};
</script>
