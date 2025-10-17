/* global $V */
'use strict'

const DB = $V.db.cmym
const Common = require('./Common')
const bcrypt = require('bcrypt')
const PERMISO = require('./PermisoSchema')
const PERFIL = require('./PerfilSchema')
const ROL = require('./RolSchema')

const CuentaSchema = DB.Schema(Object.assign({}, Common, {
  id: {
    type: String,
    unique: true
  },
  empleado_id: {
    type: String,
    default: null
  },
  nick: {
    type: String,
    default: null
  },
  email: {
    type: String,
    default: null
  },
  contrasena: {
    type: String,
    default: ''
  },
  dni: {
    type: String,
    default: null
  },
  rol: {
    type: String,
    enum: ['SuperAdmin', 'administrador', 'profesional', 'tecnico', 'supervisor', 'usuario'],
    default: 'usuario'
  },
  estado: {
    type: String,
    enum: ['activa', 'suspendida', 'inactiva'],
    default: 'inactiva'
  },
  tipo: {
    type: String,
    enum: ['interna', 'cliente', 'profesional', 'crm-cliente'],
    default: 'cliente'
  },
  empresas: [{
    type: String
  }],
  canales: [],
  centro_key: {
    type: String,
    default: null
  }
}), {
  collection: 'cuentas',
  toJSON: { virtuals: true },
  toObject: { virtuals: true },
  versionKey: false
})

/* Virtuals */
CuentaSchema.index({ id: 1 })

CuentaSchema.virtual('$link').get(function () {
  return `/v2.0/cuentas/${this.id}`
})

// Define virtuals in CuentaSchema
CuentaSchema.virtual('permisos', {
  ref: 'Permiso',
  localField: 'id',
  foreignField: 'cuenta_id',
});

CuentaSchema.virtual('perfil', {
  ref: 'Perfil',
  localField: 'id',
  foreignField: 'cuenta_id',
  justOne: true,
});

// Enable virtuals in JSON output
CuentaSchema.set('toJSON', { virtuals: true });
CuentaSchema.set('toObject', { virtuals: true });


// Middleware function to auto-populate fields
function autoPopulateFields(next) {
  this.populate('permisos').populate('perfil');
  next();
}

// Apply the middleware to find and findOne queries
CuentaSchema.pre('findOne', autoPopulateFields);
CuentaSchema.pre('find', autoPopulateFields);



/* Methods */
CuentaSchema.methods.generarHash = (pass) => {
  return bcrypt.hashSync(pass, bcrypt.genSaltSync(11))
}

CuentaSchema.methods.compararHash = (pass, hash) => {
  return bcrypt.compareSync(pass, hash)
}


module.exports = DB.model('Cuenta', CuentaSchema)
