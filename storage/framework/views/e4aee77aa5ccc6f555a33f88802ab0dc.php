<?php
    $prefix = $fieldIdPrefix ?? '';
    $empleadoData = $empleado ?? [];
    $primerNombre = old('primer_nombre', $empleadoData['primer_nombre'] ?? ($empleadoData['primerNombre'] ?? ''));
    $segundoNombre = old('segundo_nombre', $empleadoData['segundo_nombre'] ?? ($empleadoData['segundoNombre'] ?? ''));
    $primerApellido = old(
        'primer_apellido',
        $empleadoData['primer_apellido'] ?? ($empleadoData['primerApellido'] ?? ''),
    );
    $segundoApellido = old(
        'segundo_apellido',
        $empleadoData['segundo_apellido'] ?? ($empleadoData['segundoApellido'] ?? ''),
    );
    $numeroDocumento = old('numero_documento', $empleadoData['numero_documento'] ?? ($empleadoData['dni'] ?? ''));
    $tipoDocumento = old('tipo_documento', $empleadoData['tipo_documento'] ?? 'CC');
    $genero = old('genero', $empleadoData['genero'] ?? '');
    $email = old('email', $empleadoData['email'] ?? '');
    $telefono = old('telefono', $empleadoData['telefono'] ?? '');
    $cargo = old('cargo', $empleadoData['cargo'] ?? '');
    $tipoCargo = old('tipo_cargo', $empleadoData['tipo_cargo'] ?? '');

    // Extraer los _key de las estructuras anidadas si existen
    $areaId = old('area_id', $empleadoData['area_id'] ?? ($empleadoData['area_key'] ?? ''));
    $procesoId = old('proceso_id', $empleadoData['proceso_id'] ?? ($empleadoData['proceso_key'] ?? ''));
    $centroId = old('centro_id', $empleadoData['centro_id'] ?? ($empleadoData['centro_key'] ?? ''));

    $ciudad = old('ciudad', $empleadoData['ciudad'] ?? '');
    $psicosocialTipo = old('psicosocial_tipo', $empleadoData['psicosocial_tipo'] ?? '');
    $direccion = old('direccion', $empleadoData['direccion'] ?? '');
?>

<div class="row g-2">
    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user text-primary me-1" style="font-size: 11px;"></i>
            Primer Nombre <span class="text-danger">*</span>
        </label>
        <input type="text" id="<?php echo e($prefix); ?>primer_nombre" name="primer_nombre"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['primer_nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($primerNombre); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['primer_nombre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user text-secondary me-1" style="font-size: 11px;"></i>
            Segundo Nombre
        </label>
        <input type="text" id="<?php echo e($prefix); ?>segundo_nombre" name="segundo_nombre"
            class="form-control form-control-sm shadow-sm" value="<?php echo e($segundoNombre); ?>"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tag text-primary me-1" style="font-size: 11px;"></i>
            Primer Apellido <span class="text-danger">*</span>
        </label>
        <input type="text" id="<?php echo e($prefix); ?>primer_apellido" name="primer_apellido"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['primer_apellido'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($primerApellido); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['primer_apellido'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tag text-secondary me-1" style="font-size: 11px;"></i>
            Segundo Apellido
        </label>
        <input type="text" id="<?php echo e($prefix); ?>segundo_apellido" name="segundo_apellido"
            class="form-control form-control-sm shadow-sm" value="<?php echo e($segundoApellido); ?>"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-id-card text-info me-1" style="font-size: 11px;"></i>
            DNI <span class="text-danger">*</span>
        </label>
        <input type="text" id="<?php echo e($prefix); ?>numero_documento" name="numero_documento"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['numero_documento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($numeroDocumento); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['numero_documento'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-address-card text-secondary me-1" style="font-size: 11px;"></i>
            Tipo Documento
        </label>
        <select id="<?php echo e($prefix); ?>tipo_documento" name="tipo_documento"
            class="form-select form-select-sm shadow-sm"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="CC" <?php if($tipoDocumento === 'CC'): echo 'selected'; endif; ?>>CC</option>
            <option value="CE" <?php if($tipoDocumento === 'CE'): echo 'selected'; endif; ?>>CE</option>
            <option value="TI" <?php if($tipoDocumento === 'TI'): echo 'selected'; endif; ?>>TI</option>
            <option value="PA" <?php if($tipoDocumento === 'PA'): echo 'selected'; endif; ?>>Pasaporte</option>
        </select>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-venus-mars text-warning me-1" style="font-size: 11px;"></i>
            Género <span class="text-danger">*</span>
        </label>
        <select id="<?php echo e($prefix); ?>genero" name="genero"
            class="form-select form-select-sm shadow-sm <?php $__errorArgs = ['genero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="masculino" <?php if($genero === 'masculino'): echo 'selected'; endif; ?>>Masculino</option>
            <option value="femenino" <?php if($genero === 'femenino'): echo 'selected'; endif; ?>>Femenino</option>
            <option value="otro" <?php if($genero === 'otro'): echo 'selected'; endif; ?>>Otro</option>
        </select>
        <?php $__errorArgs = ['genero'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-envelope text-danger me-1" style="font-size: 11px;"></i>
            Email <span class="text-danger">*</span>
        </label>
        <input type="email" id="<?php echo e($prefix); ?>email" name="email"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($email); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-briefcase text-success me-1" style="font-size: 11px;"></i>
            Cargo <span class="text-danger">*</span>
        </label>
        <input type="text" id="<?php echo e($prefix); ?>cargo" name="cargo"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['cargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($cargo); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['cargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-user-tie text-primary me-1" style="font-size: 11px;"></i>
            Tipo Cargo <span class="text-danger">*</span>
        </label>
        <select id="<?php echo e($prefix); ?>tipo_cargo" name="tipo_cargo"
            class="form-select form-select-sm shadow-sm <?php $__errorArgs = ['tipo_cargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="gerencial" <?php if($tipoCargo === 'gerencial'): echo 'selected'; endif; ?>>Gerencial</option>
            <option value="profesional" <?php if($tipoCargo === 'profesional'): echo 'selected'; endif; ?>>Profesional</option>
            <option value="tecnico" <?php if($tipoCargo === 'tecnico'): echo 'selected'; endif; ?>>Técnico</option>
            <option value="auxiliar" <?php if($tipoCargo === 'auxiliar'): echo 'selected'; endif; ?>>Auxiliar</option>
            <option value="operativo" <?php if($tipoCargo === 'operativo'): echo 'selected'; endif; ?>>Operativo</option>
        </select>
        <?php $__errorArgs = ['tipo_cargo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-sitemap text-info me-1" style="font-size: 11px;"></i>
            Área <span class="text-danger">*</span>
        </label>
        <select id="<?php echo e($prefix); ?>area_id" name="area_id"
            class="form-select form-select-sm shadow-sm <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if (is_array($area)) {
                        $areaIdValue = isset($area['_id']) ? (string) $area['_id'] : '';
                        $areaNombre = $area['nombre'] ?? '';
                    } else {
                        $areaIdValue = isset($area->_id) ? (string) $area->_id : '';
                        $areaNombre = $area->nombre ?? '';
                    }
                ?>
                <option value="<?php echo e($areaIdValue); ?>" <?php if($areaId === $areaIdValue): echo 'selected'; endif; ?>><?php echo e($areaNombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['area_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-project-diagram text-secondary me-1" style="font-size: 11px;"></i>
            Proceso
        </label>
        <select id="<?php echo e($prefix); ?>proceso_id" name="proceso_id" class="form-select form-select-sm shadow-sm"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <?php $__currentLoopData = $procesos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proceso): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if (is_array($proceso)) {
                        $procesoIdValue = isset($proceso['_id']) ? (string) $proceso['_id'] : '';
                        $procesoNombre = $proceso['nombre'] ?? '';
                    } else {
                        $procesoIdValue = isset($proceso->_id) ? (string) $proceso->_id : '';
                        $procesoNombre = $proceso->nombre ?? '';
                    }
                ?>
                <option value="<?php echo e($procesoIdValue); ?>" <?php if($procesoId === $procesoIdValue): echo 'selected'; endif; ?>><?php echo e($procesoNombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-building text-warning me-1" style="font-size: 11px;"></i>
            Sede <span class="text-danger">*</span>
        </label>
        <select id="<?php echo e($prefix); ?>centro_id" name="centro_id"
            class="form-select form-select-sm shadow-sm <?php $__errorArgs = ['centro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <?php $__currentLoopData = $centros; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $centro): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    if (is_array($centro)) {
                        $centroIdValue = isset($centro['_id']) ? (string) $centro['_id'] : '';
                        $centroNombre = $centro['nombre'] ?? '';
                    } else {
                        $centroIdValue = isset($centro->_id) ? (string) $centro->_id : '';
                        $centroNombre = $centro->nombre ?? '';
                    }
                ?>
                <option value="<?php echo e($centroIdValue); ?>" <?php if($centroId === $centroIdValue): echo 'selected'; endif; ?>><?php echo e($centroNombre); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <?php $__errorArgs = ['centro_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-city text-success me-1" style="font-size: 11px;"></i>
            Ciudad <span class="text-danger">*</span>
        </label>
        <input type="text" id="<?php echo e($prefix); ?>ciudad" name="ciudad"
            class="form-control form-control-sm shadow-sm <?php $__errorArgs = ['ciudad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            value="<?php echo e($ciudad); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
        <?php $__errorArgs = ['ciudad'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-clipboard-list text-primary me-1" style="font-size: 11px;"></i>
            Tipo Prueba <span class="text-danger">*</span>
        </label>
        <select id="<?php echo e($prefix); ?>psicosocial_tipo" name="psicosocial_tipo"
            class="form-select form-select-sm shadow-sm <?php $__errorArgs = ['psicosocial_tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
            <option value="">Seleccionar...</option>
            <option value="A" <?php if($psicosocialTipo === 'A'): echo 'selected'; endif; ?>>Tipo A</option>
            <option value="B" <?php if($psicosocialTipo === 'B'): echo 'selected'; endif; ?>>Tipo B</option>
        </select>
        <?php $__errorArgs = ['psicosocial_tipo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <div class="invalid-feedback" style="font-size: 11px;"><?php echo e($message); ?></div>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-phone text-info me-1" style="font-size: 11px;"></i>
            Teléfono
        </label>
        <input type="text" id="<?php echo e($prefix); ?>telefono" name="telefono"
            class="form-control form-control-sm shadow-sm" value="<?php echo e($telefono); ?>"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>

    
    <div class="col-12">
        <label class="form-label fw-semibold text-dark mb-1" style="font-size: 13px;">
            <i class="fas fa-map-marker-alt text-danger me-1" style="font-size: 11px;"></i>
            Dirección
        </label>
        <input type="text" id="<?php echo e($prefix); ?>direccion" name="direccion"
            class="form-control form-control-sm shadow-sm" value="<?php echo e($direccion); ?>"
            style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 10px; font-size: 13px;">
    </div>
</div>
<?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/admin/gestion-administrativa/empresa/empleados/_form.blade.php ENDPATH**/ ?>