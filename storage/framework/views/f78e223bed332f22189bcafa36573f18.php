

<?php $__env->startSection('title', 'Página no encontrada'); ?>

<?php $__env->startSection('content'); ?>
<div class="error-page">
    <div class="error-content">
        <div class="error-code">
            <h1>404</h1>
        </div>
        <div class="error-message">
            <h2>Página no encontrada</h2>
            <p>Lo sentimos, la página que está buscando no se pudo encontrar.</p>
            <p>Es posible que haya sido movida, eliminada o que la URL sea incorrecta.</p>
        </div>
        <div class="error-actions">
            <a href="<?php echo e(route('dashboard')); ?>" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ir al Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Regresar
            </a>
        </div>
    </div>
</div>

<style>
.error-page {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: linear-gradient(135deg,  #D1A854, #EDC979 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.error-content {
    text-align: center;
    color: white;
    max-width: 600px;
    padding: 2rem;
}

.error-code h1 {
    font-size: 8rem;
    font-weight: bold;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: pulse 2s infinite;
}

.error-message h2 {
    font-size: 2.5rem;
    margin: 1rem 0;
    font-weight: 300;
}

.error-message p {
    font-size: 1.2rem;
    margin: 1rem 0;
    opacity: 0.9;
    line-height: 1.6;
}

.error-actions {
    margin-top: 2rem;
}

.error-actions .btn {
    margin: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary {
    background: rgba(255,255,255,0.2);
    border: 2px solid white;
    color: white;
}

.btn-primary:hover {
    background: white;
    color: #667eea;
}

.btn-secondary {
    background: transparent;
    border: 2px solid rgba(255,255,255,0.5);
    color: white;
}

.btn-secondary:hover {
    background: rgba(255,255,255,0.1);
    border-color: white;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

@media (max-width: 768px) {
    .error-code h1 {
        font-size: 5rem;
    }
    
    .error-message h2 {
        font-size: 2rem;
    }
    
    .error-message p {
        font-size: 1rem;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\José Espinel\OneDrive - CM&M ASESORES DE SEGURO LIMITADA\Documentos - Grupo IT\APP-GIR365-V2\resources\views/errors/404.blade.php ENDPATH**/ ?>