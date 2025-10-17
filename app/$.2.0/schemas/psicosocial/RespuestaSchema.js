/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')
const PREGUNTA = require('./PreguntaSchema')

const RespuestaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  diagnostico_id: {
    type: String,
    default: null
  },
  empleado_id: {
    type: String,
    default: null
  },
  hoja_id: {
    type: String,
    default: null
  },
  pregunta_id: {
    type: String,
    default: null
  },
  consecutivo: {
    type: Number,
    default: 0
  },
  tipo: {
    type: String,
    enum: ['intralaboral', 'extralaboral', 'estres'],
    default: 'intralaboral'
  },
  valor: {
    type: Number,
    default: 0
  },
  opcion: {
    type: String,
    enum: ['siempre', 'casi_siempre', 'algunas_veces', 'casi_nunca', 'nunca'],
    default: 'siempre'
  }
}), {
  collection: 'respuestas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

RespuestaSchema.index({ id: 1 })

RespuestaSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/respuestas/${this.id}`
})

RespuestaSchema.pre('init', function (doc) {
  PREGUNTA.findOne({ id: doc.pregunta_id })
    .select('id tipo consecutivo enunciado')
    .then((pregunta) => {
      doc.$pregunta = (pregunta) ? pregunta : null

    }).catch(err => {
      err.status = 500

    })
})

module.exports = DB.model('Respuesta', RespuestaSchema)
