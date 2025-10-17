/* global $V */
'use strict'

const DB = $V.db.cmym
const Common = require('./Common')

const NotificacionSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  vista: {
    type: Boolean,
    default: false
  },
  link: {
    type: String,
    default: null
  },
  titulo: {
    type: String,
    default: null
  },
  descripcion: {
    type: String,
    default: null
  },
  modulo: {
    type: String,
    enum: ['hallazgos', 'psicosocial', 'plan-trabajo'],
    default: 'plan-trabajo'
  },
  canales: []
}), {
  collection: 'notificaciones',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
NotificacionSchema.index({ id: 1 })

NotificacionSchema.virtual('$link').get(function () {
  return `/v2.0/notificaciones/${this.id}`
})

module.exports = DB.model('Notificacion', NotificacionSchema)
