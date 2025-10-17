const DB = $V.db.empresas
const Common = require('./Common')

const UsuariaSchema = DB.Schema(Object.assign({}, Common, {
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
  collection: 'usuarias',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
UsuariaSchema.index({ id: 1 })

UsuariaSchema.virtual('$link').get(function () {
  return `/${this.empresa_id}/usuarias/${this.id}`
})

module.exports = DB.model('Usuaria', UsuariaSchema)
