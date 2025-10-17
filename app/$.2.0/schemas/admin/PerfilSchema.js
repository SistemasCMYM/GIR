/* global $V */
'use strict'

const DB = $V.db.cmym
const Common = require('./Common')

const PerfilSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  cuenta_id: {
    type: String,
    default: null
  },
  nombre: {
    type: String,
    default: null
  },
  apellido: {
    type: String,
    default: null
  },
  genero: {
    type: String,
    enum: ['masculino', 'femenino', 'otro'],
    default: null
  },
  ocupacion: {
    type: String,
    default: null
  },
  firma: {
    type: String,
    default: ''
  },
  pieFirma: {
    type: String,
    default: ''
  },
  licencia: {
    type: String,
    default: ''
  }
}), {
  collection: 'perfiles',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
PerfilSchema.index({ id: 1 })

PerfilSchema.virtual('$link').get(function () {
  return `/v2.0/cuenta/perfil/${this.id}`
})

module.exports = DB.model('Perfil', PerfilSchema)
