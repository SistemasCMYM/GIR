/* global $V */
'use strict'

const DB = $V.db.cmym
const Common = require('./Common')

const SesionSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  key: {
    type: String,
    default: null
  },
  empleado_id: {
    type: String,
    default: null
  },
  empresa_id: {
    type: String,
    default: null
  },
  cuenta_id: {
    type: String,
    default: null
  },
  ip: {
    type: String,
    default: null
  },
  expira: {
    type: Date,
    default: new Date()
  },
  _fechaUltimoAcceso: {
    type: Date,
    default: null
  }
}), {
  collection: 'sesiones',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
SesionSchema.index({ id: 1 })

module.exports = DB.model('Sesion', SesionSchema)
