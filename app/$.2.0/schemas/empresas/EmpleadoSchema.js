/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const EmpleadoSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    required: true,
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
  sede_key: {
    type: String,
    default: null
  },
  sede_label: {
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
  primerNombre: {
    type: String,
    default: null
  },
  segundoNombre: {
    type: String,
    default: null
  },
  primerApellido: {
    type: String,
    default: null
  },
  segundoApellido: {
    type: String,
    default: null
  },
  dni: {
    type: String,
    default: null
  },
  email: {
    type: String,
    default: 'no email',
  },
  genero: {
    type: String,
    enum: ['masculino', 'femenino', 'otro'],
    default: 'masculino'
  },
  tipo_cargo: {
    type: String,
    enum: ['gerencial', 'profesional/tecnico', 'auxiliar', 'operativo']
  },
  usuaria_label: {
    type: String,
    default: null
  },
  usuaria_key: {
    type: String,
    default: null
  },
  cargo: {
    type: String,
    default: null
  },
  psicosocial: {
    type: Boolean,
    default: true
  },
  psicosocial_tipo: {
    type: String,
    enum: ['A', 'B'],
    default: 'A'
  }
}), {
  collection: 'empleados',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
EmpleadoSchema.index({ id: 1 })

EmpleadoSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/empleados/${this.id}`
})

module.exports = DB.model('Empleado', EmpleadoSchema)
