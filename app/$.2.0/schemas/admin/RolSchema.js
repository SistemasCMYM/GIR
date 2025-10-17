// RolSchema.js
// Schema Mongoose para roles, construido a partir de app/Models/Auth/Rol.php
// Ruta: app/$.2.0/schemas/admin/RolSchema.js

const { Schema } = require('mongoose');

// Valores permitidos (copiados desde Rol::$roles y Rol::$tipos y Rol::$modulos / $permisos donde aplica)
const ROLES = [
  'SuperAdmin',
  'administrador',
  'profesional',
  'tecnico',
  'supervisor',
  'usuario'
];

const TIPOS = [
  'interna',
  'cliente',
  'profesional',
  'usuario'
];

const MODULOS = [
  'dashboard',
  'administracion',
  'hallazgos',
  'psicosocial',
  'configuracion',
  'informes'
];

const PERMISOS = [
  'all', 'create', 'read', 'update', 'delete', 'admin', 'write'
];

const RolSchema = new Schema({
  id: { type: String, required: true, index: true }, // id string compatible con Node.js (no ObjectId)
  nombre: { type: String, required: true, enum: ROLES },
  descripcion: { type: String, default: '' },
  tipo: { type: String, required: true, enum: TIPOS, default: 'usuario' },
  empresa_id: { type: String, default: null },
  cuenta_id: { type: String, default: null },
  modulos: { type: [String], default: ['dashboard'], enum: MODULOS },
  permisos: { type: [String], default: [], enum: PERMISOS },
  activo: { type: Boolean, default: true },
  created_at: { type: Date, default: () => new Date() },
  updated_at: { type: Date, default: () => new Date() }
});

// Índices y helpers
RolSchema.index({ id: 1 }, { unique: true });

// Virtual similar to getLinkAttribute en PHP
RolSchema.virtual('link').get(function() {
  return `/v2.0/roles/${this.id}`;
});

// Método para convertir a estructura compatible con la respuesta que espera el frontend
RolSchema.methods.toNodejsObject = function() {
  return {
    id: this.id,
    nombre: this.nombre,
    descripcion: this.descripcion,
    tipo: this.tipo,
    empresa_id: this.empresa_id,
    cuenta_id: this.cuenta_id,
    modulos: this.modulos || [],
    permisos: this.permisos || [],
    activo: this.activo,
    created_at: this.created_at,
    updated_at: this.updated_at,
    $link: this.link
  };
};

module.exports = { RolSchema, ROLES, TIPOS, MODULOS, PERMISOS };
