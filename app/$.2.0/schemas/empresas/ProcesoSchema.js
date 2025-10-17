/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const ProcesoSchema = DB.Schema(Object.assign({}, Common, {
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
  descripcion: {
    type: String,
    default: null
  },
  key: {
    type: String,
    default: ''
  }
}), {
  collection: 'procesos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
ProcesoSchema.index({ id: 1 })

ProcesoSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/procesos/${this.id}`
})

module.exports = DB.model('Proceso', ProcesoSchema)
