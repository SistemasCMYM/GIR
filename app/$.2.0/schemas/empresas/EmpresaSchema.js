/* global $V */
'use strict'

const DB = $V.db.empresas
const Common = require('./Common')

const EmpresaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  activo: {
    type: Boolean,
    default: true
  },
  grupo_empresarial: {
    type: Boolean,
    default: false
  },
  grupo_key: {
    type: String,
    default: ''
  },
  grupo_label: {
    type: String,
    default: ''
  },
  centro_key: {
    type: String,
    default: ''
  },
  centro_label: {
    type: String,
    default: 'No aplica'
  },
  sector_key: {
    type: String,
    default: 'default'
  },
  sector_label: {
    type: String,
    default: 'No aplica'
  },
  nombre: {
    type: String,
    default: null
  },
  nit: {
    type: String,
    default: null
  },
  web: {
    type: String,
    default: null
  },
  logo: {
    type: String,
    default: null
  },
  logo_activo: {
    type: Boolean,
    default: false
  },
  colorPrimario: {
    type: String,
    default: "#E51F2E"
  },
  colorSecundario: {
    type: String,
    default: "#212121"
  },
  landingBlanco: {
    type: Boolean,
    default: false
  },
  tipo: {
    type: String,
    enum: ['juridica', 'natural'],
    default: 'juridica'
  },
  clasificacion: {
    type: String,
    enun: ['alfa', 'beta', 'gamma', 'omega', 'sin_clasificar'],
    default: 'sin_clasificar'
  },
  cantidad_trabajadores: {
    type: Number,
    default: 0
  },
  estado: {
    type: String,
    enum: ['cliente', 'retirado', 'prospecto', 'reintegrado'],
    default: 'cliente'
  },
  regional: {
    type: String,
    default: 'nacional'
  },
  aniversario: {
    type: Date,
    default: ''
  },
  servicios_actuales: [{
    type: String
  }],
  modulos: [{
    type: String
  }],
  config: {
    hallazgos_custom: {
      type: Boolean,
      default: false  
    },
    cuenta_por_centros: {
      type: Boolean,
      default: false  
    },
    variables_tareas: {
      type: Boolean,
      default: false
    },
    psicosocial_filtros: {
      type: Boolean,
      default: false
    },
    psicosocial_filtro_area: {
      type: Boolean,
      default: false
    },
    psicosocial_filtro_cuidad: {
      type: Boolean,
      default: false
    },
    psicosocial_filtro_sede: {
      type: Boolean,
      default: false
    },
    psicosocial_filtro_proceso: {
      type: Boolean,
      default: false
    }
  }
}), {
  collection: 'empresas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
EmpresaSchema.index({ id: 1 })

EmpresaSchema.virtual('$link').get(function () {
  return `/${this.id}`
})

module.exports = DB.model('Empresa', EmpresaSchema)
