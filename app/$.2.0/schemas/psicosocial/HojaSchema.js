/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')

const HojaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  diagnostico_id: {
    type: String,
    default: null
  },
  empleado_id: {
    type: String,
    default: null
  },
  datos_id: {
    type: String,
    default: null
  },
  area_key: {
    type: String,
    default: null
  },
  area_label: {
    type: String,
    default: ''
  },
  centro_key: {
    type: String,
    default: null
  },
  centro_label: {
    type: String,
    default: ''
  },
  sede_key: {
    type: String,
    default: null
  },
  sede_label: {
    type: String,
    default: ''
  },
  contrato_key: {
    type: String,
    default: null
  },
  contrato_label: {
    type: String,
    default: ''
  },
  proceso_key: {
    type: String,
    default: null
  },
  proceso_label: {
    type: String,
    default: ''
  },
  ciudad_key: {
    type: String,
    default: null
  },
  ciudad_label: {
    type: String,
    default: ''
  },
  usuaria_key: {
    type: String,
    default: null
  },
  usuaria_label: {
    type: String,
    default: ''
  },
  dni: {
    type: String,
    default: null
  },
  nombre: {
    type: String,
    default: 'Sin definir'
  },
  completado: {
    type: Boolean,
    default: false
  },
  intralaboral_tipo: {
    type: String,
    enum: ['A', 'B'],
    default: 'A'
  },
  extralaboral_tipo: {
    type: String,
    enum: ['A', 'B', 'VERSION_ANTERIOR'],
    default: 'VERSION_ANTERIOR'
  },
  estres_tipo: {
    type: String,
    enum: ['A', 'B'],
    default: 'A'
  },
  intralaboral_consecutivo: {
    type: Number,
    default: 1
  },
  extralaboral_consecutivo: {
    type: Number,
    default: 1
  },
  estres_consecutivo: {
    type: Number,
    default: 1
  },
  intralaboral: {
    type: String,
    enum: ['pendiente', 'en_progreso', 'completado'],
    default: 'pendiente'
  },
  extralaboral: {
    type: String,
    enum: ['pendiente', 'en_progreso', 'completado'],
    default: 'pendiente'
  },
  estres: {
    type: String,
    enum: ['pendiente', 'en_progreso', 'completado'],
    default: 'pendiente'
  },
  datos: {
    type: String,
    enum: ['pendiente', 'en_progreso', 'completado'],
    default: 'pendiente'
  },
  puntaje_intralaboral: {
    /* Dimensiones */
    dim_caracteristicasDelLiderazgo: { type: Number, default: 0 },
    dim_relacionesSocialesEnElTrabajo: { type: Number, default: 0 },
    dim_retroalimentacionDeDesempeno: { type: Number, default: 0 },
    dim_relacionConLosColaboradores: { type: Number, default: 0 },
    dim_claridadDelRol: { type: Number, default: 0 },
    dim_capacitacion: { type: Number, default: 0 },
    dim_participacionManejoDelCambio: { type: Number, default: 0 },
    dim_desarrolloHabilidades: { type: Number, default: 0 },
    dim_autonomiaSobreElTrabajo: { type: Number, default: 0 },
    dim_ambientalesYEsfuerzoFisico: { type: Number, default: 0 },
    dim_demandasEmocionales: { type: Number, default: 0 },
    dim_demandasCuantitativas: { type: Number, default: 0 },
    dim_trabajoSobreExtralaboral: { type: Number, default: 0 },
    dim_exigenciasResponsabilidadCargo: { type: Number, default: 0 },
    dim_demandasCargaMental: { type: Number, default: 0 },
    dim_consistenciaDelRol: { type: Number, default: 0 },
    dim_demandasJornadaTrabajo: { type: Number, default: 0 },
    dim_recompensasPorTrabajo: { type: Number, default: 0 },
    dim_reconocimientoYCompensacion: { type: Number, default: 0 },
    dim_liderazgoRelacionesSociales: { type: Number, default: 0 },
    dim_controlSobreElTrabajo: { type: Number, default: 0 },
    dim_demandasDelTrabajo: { type: Number, default: 0 },
    dim_recompensas: { type: Number, default: 0 },
    /* Dominios */
    dom_liderazgoRelacionesSociales: { type: Number, default: 0 },
    dom_controlSobreElTrabajo: { type: Number, default: 0 },
    dom_demandasDelTrabajo: { type: Number, default: 0 },
    dom_recompensas: { type: Number, default: 0 },
    total: { type: Number, default: 0 }
  },
  puntaje_extralaboral: {
    dim_tiempoFueraDelTrabajo: { type: Number, default: 0 },
    dim_relacionesFamiliares: { type: Number, default: 0 },
    dim_relacionesInterpersonales: { type: Number, default: 0 },
    dim_situacionEconomica: { type: Number, default: 0 },
    dim_caracteristicasVivienda: { type: Number, default: 0 },
    dim_influenciaTrabajo: { type: Number, default: 0 },
    dim_dezplazamiento: { type: Number, default: 0 },
    total: { type: Number, default: 0 }
  },
  puntaje_estres: {
    a: { type: Number, default: 0 },
    b: { type: Number, default: 0 },
    c: { type: Number, default: 0 },
    d: { type: Number, default: 0 },
    total: { type: Number, default: 0 },
  },
  intralaboral_psicologo: {
    recomendaciones: { type: String, default: '' },
    observaciones: { type: String, default: '' }
  },
  extralaboral_psicologo: {
    recomendaciones: { type: String, default: '' },
    observaciones: { type: String, default: '' }
  },
  estres_psicologo: {
    recomendaciones: { type: String, default: '' },
    observaciones: { type: String, default: '' }
  },
  consentimiento: {
    type: Boolean,
    default: false
  },
  fecha_consentimiento: {
    type: Date,
    default: null
  }
}), {
  collection: 'hojas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

HojaSchema.index({ id: 1 })

HojaSchema.virtual('$link').get(function () {
  return `/v2.0/psicosocial/hojas/${this.id}`
})

module.exports = DB.model('Hoja', HojaSchema)
