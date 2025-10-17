/* global $V */
'use strict'

const DB = $V.db.cmym
const Common = require('./Common')

const PermisoSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  cuenta_id: {
    type: String,
    default: null
  },
  modulo: {
    type: String,
    default: null
  },
  tipo: {
    type: String,
    enum: ['usuario', 'app'],
    default: 'usuario'
  },
  acciones: [{
    type: String
  }],
  link: {
    type: String,
    default: null
  }
}), {
  collection: 'permisos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
PermisoSchema.index({ id: 1 })

PermisoSchema.virtual('$link').get(function () {
  return `/v2.0/permisos/${this.id}`
})

module.exports = DB.model('Permiso', PermisoSchema)
