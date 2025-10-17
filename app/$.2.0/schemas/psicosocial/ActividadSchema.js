/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')

const ActividadScehma = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  profesional_id: {
    type: String,
    default: null
  },
  diagnostico_id: {
    type: String,
    default: null
  },
  categoria: {
    type: String,
    enum: ['intralaboral', 'extralaboral', 'estres']
  },
  tipo: {
    type: String,
    enum: ['prevencion-promocion', 'intervencion'],
    default: 'intervencion'
  },
  actividad: {
    type: String,
    default: ''
  },
  recomendacion: {
    type: String,
    default: ''
  },
  objetivo: {
    type: String,
    default: ''
  },
  horas: {
    type: String,
    default: ''
  },
  variables: {
    type: String,
    enum: ['organizacion', 'grupo', 'lideres', 'individual'],
    default: 'organizacion'
  },
  horas_profesional: {
    type: Number,
    default: 0
  },
  requerimientos: [{
    nombre: {
      type: String,
      default: null
    }
  }]
}), {
  collection: 'actividades',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

ActividadScehma.index({ id: 1 })

ActividadScehma.virtual('$link').get(function () {
  return `/m/psicosocial/intervenciones/actividades/${this.id}`
})

module.exports = DB.model('Actividad', ActividadScehma)
