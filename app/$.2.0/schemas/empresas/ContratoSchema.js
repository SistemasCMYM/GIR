const DB = $V.db.empresas
const Common = require('./Common')

const ContratoSchema = DB.Schema(Object.assign({}, Common, {
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
  collection: 'contratos',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
ContratoSchema.index({ id: 1 })

ContratoSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/contratos/${this.id}`
})

module.exports = DB.model('Contrato', ContratoSchema)
