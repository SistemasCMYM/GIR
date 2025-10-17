/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const SectorSchema = DB.Schema(Object.assign({}, Common, {
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
  }
}), {
  collection: 'sectores',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

SectorSchema.index({ id: 1 })

SectorSchema.virtual('$link').get(function () {
  return `/v2.0/empresas/sectores/${this.id}`
})

module.exports = DB.model('Sector', SectorSchema)
