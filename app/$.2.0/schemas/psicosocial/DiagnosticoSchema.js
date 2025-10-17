/* global $V */
'use strict'

const DB = $V.db.psicosocial
const Common = require('./Common')

const DiagnosticoSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empresa_id: {
    type: String,
    default: null
  },
  profesional_id: {
    type: String,
    default: ''
  },
  area_key: {
    type: String,
    default: null
  },
  area_label: {
    type: String,
    defualt: ''
  },
  contrato_key: {
    type: String,
    default: null
  },
  contrato_label: {
    type: String,
    defualt: ''
  },
  centro_key: {
    type: String,
    default: null
  },
  centro_label: {
    type: String,
    defualt: ''
  },
  ciudad_key: {
    type: String,
    default: null
  },
  ciudad_label: {
    type: String,
    defualt: ''
  },
  proceso_key: {
    type: String,
    default: null
  },
  proceso_label: {
    type: String,
    defualt: ''
  },
  filtro: {
    type: Boolean,
    default: false
  },
  filtro_key: {
    type: String,
    default: null
  },
  clave: {
    type: String,
    default: null
  },
  descripcion: {
    type: String,
    default: ''
  },
  grupo: {
    type: String,
    default: null
  },
  cierre: {
    type: Boolean,
    default: false
  },
  objetivo: {
    type: String,
    default: ''
  },
  objetivos_especificos: [{
    nombre: {
      type: String,
      default: null
    }
  }],
  metodologia: {
    type: String,
    default: ''
  },
  observaciones: {
    type: String,
    default: ''
  },
  recomendaciones: {
    type: String,
    default: ''
  },
  informe: {
    url: {
      type: String,
      default: null
    },
    mimetype: {
      type: String,
      default: null
    },
    bucket: {
      type: String,
      default: null
    },
    key:  {
      type: String,
      default: null
    },
    nombre: {
      type: String,
      default: null
    },
    tamano: {
      type: String,
      default: null
    }
  },
}), {
  collection: 'diagnosticos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

DiagnosticoSchema.index({ id: 1 })

DiagnosticoSchema.virtual('$link').get(function () {
  return `/v2.0/psicosocial/diagnosticos/${this.id}`
})

module.exports = DB.model('Diagnostico', DiagnosticoSchema)
