/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const GrupoSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  nombre: {
    type: String,
    default: ''
  },
  descripcion: {
    type: String,
    default: ''
  },
}), {
  collection: 'grupos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
GrupoSchema.index({ id: 1 })

module.exports = DB.model('Grupo', GrupoSchema)
