'use strict'

/**
 *  Commom Fields in all Schemas
 */
module.exports = {
  '_fechaCreado': {
    'type': Date,
    'default': null
  },
  '_creadoPor': {
    'type': Date,
    'default': null
  },
  '_fechaBorrado': {
    'type': Date,
    'default': null
  },
  '_borradoPor': {
    'type': Date,
    'default': null
  },
  '_fechaModificado': {
    'type': Date,
    'default': null
  },
  '_modificadoPor': {
    'type': Date,
    'default': null
  },
  '_esPublico': {
    'type': Boolean,
    'default': false
  },
  '_esBorrado': {
    'type': Boolean,
    'default': false
  },
  '_slug': {
    'type': String,
    'default': ''
  },
  '_tags': []
}
