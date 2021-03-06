<?php

namespace App\Http\Controllers\Api;

use App\Acuerdo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Reunion;
use App\Tema;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReunionesMinutaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $reunion_id)
    {
        // return response(['mensaje' => 'ok'], 401);
        $reunion = Reunion::findOrFail($reunion_id);
        $this->authorize('update', $reunion);
        $datos = json_decode($request->data, true);

        // Creamos objetos de fecha para cada fecha_compromiso de los acuerdos
        foreach ($datos['temas'] as $keyTema => $tema) {
            foreach ($tema['acuerdos'] as $keyAcuerdo => $acuerdo) {
                $datos['temas'][$keyTema]['acuerdos'][$keyAcuerdo]['fecha_compromiso'] = 
                    Carbon::createFromFormat('d/m/Y', $acuerdo['fecha_compromiso']);
            }
        }

        Validator::make($datos, [
            'temas' => 'required',
            'temas.*.comentario' => 'required|min:3|max:500',
            'temas.*.acuerdos.*.descripcion' => 'min:3|max:191',
            'temas.*.acuerdos.*.fecha_compromiso' => 'after:'.now(),
            'miembros_que_asistieron_ids' => 'required',
            'miembros_que_asistieron_ids.*' => Rule::in($reunion->convocados->pluck('id')),
            'invitados_externos_que_asistieron_ids.*' => Rule::in($reunion->invitadosExternos->pluck('id')),
        ])->validate();

        return DB::transaction(function () use ($datos, $reunion) {
            // Actualizar lista de asistentes
            $reunion->convocados()
                    ->each(function ($asistente) use ($datos){
                        if( in_array($asistente->id, $datos['miembros_que_asistieron_ids']) ){
                            $asistente->asistencia->asistio = true;
                        } else
                            $asistente->asistencia->asistio = false;
                        $asistente->asistencia->save();
                    }
            );
            $reunion->invitadosExternos()
                    ->each(function ($asistente) use ($datos)
                    {
                        if( in_array($asistente->id, $datos['invitados_externos_que_asistieron_ids']) ){
                            $asistente->asistencia->asistio = true;
                        } else
                            $asistente->asistencia->asistio = false;
                        $asistente->asistencia->save();
                    }
            );
                  
            // ---- Rellenamos la información de temas con datos nuevos ----
            $reunion->temas()->delete(); 
            foreach ($datos['temas'] as $tema) {
                // Agregamos información del tema
                $temaModel = new Tema();
                $temaModel->reunion_id = $reunion->id;
                $temaModel->descripcion = $tema['descripcion'];
                $temaModel->comentario = $tema['comentario'];
                $temaModel->save();

                // Mapeamos los acuerdos a modelos tipo App\Acuerdo
                foreach($tema['acuerdos'] as $acuerdo){
                    $acuerdoModel = new Acuerdo();
                    $acuerdoModel->descripcion = $acuerdo['descripcion'];
                    $acuerdoModel->producto_esperado = $acuerdo['producto_esperado'];
                    $acuerdoModel->fecha_compromiso = $acuerdo['fecha_compromiso'];
                    $acuerdoModel->tema_id = $temaModel->id;
                    $acuerdoModel->responsable_id = $acuerdo['responsable']['id'];
                    $acuerdoModel->save();
                }
            }
            // ---- Creamos el documento pdf de la minuta ----
            $reunion->crearPDFMinuta();
            return response(['message' => 'Minuta guardada']);
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($reunion_id)
    {
        $reunion = Reunion::findOrFail($reunion_id);
        $this->authorize('eliminarMinuta', $reunion);
        $respuesta = $reunion->eliminarPDFMinuta();
        return response($respuesta);
    }
}
