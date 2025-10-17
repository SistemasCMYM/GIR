/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')

const PreguntaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  tipo: {
    type: String,
    enum: ['intralaboral_a', 'intralaboral_b', 'extralaboral', 'estres'],
    default: 'intralaboral_a'
  },
  enunciado: {
    type: String,
    default: null
  },
  consecutivo: {
    type: Number,
    default: null
  }
}), {
  collection: 'preguntas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

PreguntaSchema.index({ id: 1 })

PreguntaSchema.virtual('$link').get(function () {
  return `/v2.0/empresa/preguntas/${this.id}`
})

module.exports = DB.model('Pregunta', PreguntaSchema)
