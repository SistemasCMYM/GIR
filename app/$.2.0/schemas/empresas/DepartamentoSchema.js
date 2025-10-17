/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const DepartamentoSchema = DB.Schema(Object.assign({}, Common, {
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
  }
}), {
  collection: 'departamentos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
DepartamentoSchema.index({ id: 1 })

DepartamentoSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/deptos/${this.id}`
})

module.exports = DB.model('Departamento', DepartamentoSchema)
