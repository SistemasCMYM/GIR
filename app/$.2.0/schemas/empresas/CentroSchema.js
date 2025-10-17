/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const CentroSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  nombre: {
    type: String,
    default: null
  },
  direccion: {
    type: String,
    default: null
  },
  latitud: {
    type: String,
    default: null
  },
  longitud: {
    type: String,
    default: null
  },
  regional: {
    type: String,
    default: ''
  },
  cuidad_key: {
    type: String,
    default: null
  }
}), {
  collection: 'centros',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
CentroSchema.index({ id: 1 })

CentroSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/centros/${this.id}`
})

module.exports = DB.model('Centro', CentroSchema)
