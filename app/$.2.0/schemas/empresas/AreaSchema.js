/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const AreaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  centro_key: {
    type: String,
    default: null
  },
  nombre: {
    type: String,
    default: null
  },
  descripcion: {
    type: String,
    default: null
  }
}), {
  collection: 'areas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
AreaSchema.index({ id: 1 })

AreaSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/areas/${this.id}`
})

module.exports = DB.model('Area', AreaSchema)
