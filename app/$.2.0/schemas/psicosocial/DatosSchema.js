/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')

const DatosSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  empleado_id: {
    type: String,
    default: null
  },
  diagnostico_id: {
    type: String,
    default: null
  },
  hoja_id: {
    type: String,
    default: null
  },
  nombre: {
    type: String,
    default: null
  },
  completado: {
    type: Boolean,
    default: false
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
  centro_key: {
    type: String,
    default: null
  },
  centro_label: {
    type: String,
    default: ''
  },
  genero: {
    type: String,
    enum: ['masculino', 'femenino'],
    default: 'masculino'
  },
  fecha_nacimiento: {
    type: Number,
    default: null
  },
  edad: {
    type: String,
    default: null
  },
  estado_civil: {
    type: String,
    enum: ['solter@', 'casad@', 'union_libre', 'separad@', 'divorciad@', 'viud@', 'sacerdote_monja'],
    default: 'solter@'
  },
  nivel_estudios: {
    type: String,
    enum: [
      'ninguno',
      'primaria_incompleta',
      'primaria_completa',
      'bachillerato_incompleto',
      'bachillerato_completo',
      'tecnico_tecnologico_incompleto',
      'tecnico_tecnologico_completo',
      'profesional_incompleto',
      'profesional_completo',
      'carrera_militar_policia',
      'postgrado_incompleto',
      'postgrado_completo'
    ],
    default: 'ninguno'
  },
  profesion: {
    type: String,
    default: null
  },
  lugar_residencia: {
    type: String,
    default: null
  },
  estrato_social: {
    type: String,
    enum: ['1', '2', '3', '4', '5', '6', 'finca', 'no_se'],
    default: 'no_se'
  },
  tipo_vivienda: {
    type: String,
    enum: ['propia', 'arriendo', 'familiar', 'no_se'],
    default: 'arriendo'
  },
  dependientes_economicos: {
    type: Number,
    default: 0
  },
  lugar_trabajo: {
    type: String,
    default: null
  },
  tiempo_laborado: {
    type: String,
    enum: ['menor_igual_uno', 'uno_a_tres', 'tres_a_cinco', 'cinco_a_diez', 'mayor_diez'],
    default: 'menor_igual_uno'
  },
  nombre_cargo: {
    type: String,
    default: null
  },
  tipo_cargo: {
    type: String,
    enum: ['gerencial', 'jefatura', 'profesional', 'auxiliar', 'operativo'],
    default: 'profesional'
  },
  tiempo_en_cargo: {
    type: String,
    enum: ['menor_igual_uno', 'uno_a_tres', 'tres_a_cinco', 'cinco_a_diez', 'mayor_diez'],
    default: 'menor_igual_uno'
  },
  departamento_cargo: {
    type: String,
    default: null
  },
  tipo_contrato: {
    type: String,
    enum: [
      'temporal_menor_uno',
      'temporal_mayor_uno',
      'termino_indefinido',
      'prestacion_de_servicios',
      'no_se'
    ],
    default: 'no_se'
  },
  horas_laboradas_dia: {
    type: Number,
    default: 0
  },
  tipo_salario: {
    type: String,
    enum: ['fijo', 'fijo_variable', 'variable']
  }
}), {
  collection: 'datos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

DatosSchema.index({ id: 1 })

DatosSchema.virtual('$link').get(function () {
  return `/v2.0/psicosocial/datos/${this.id}`
})

module.exports = DB.model('Datos', DatosSchema)
