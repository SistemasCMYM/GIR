const DB = $V.db.empresas
const Common = require('./Common')

const CiudadSchema = DB.Schema(Object.assign({}, Common, {
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
  collection: 'ciudades',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
CiudadSchema.index({ id: 1 })

CiudadSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/ciudades/${this.id}`
})

module.exports = DB.model('Ciudad', CiudadSchema)
