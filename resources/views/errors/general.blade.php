@extends('layouts.error')

@section('title', 'Error del Sistema')

@section('content')
<div class="error-page">
    <div class="error-content">
        <div class="error-code">
            <h1><i class="fas fa-exclamation-triangle"></i></h1>
        </div>
        <div class="error-message">
            <h2>Error del Sistema</h2>
            <p>Ha ocurrido un error inesperado en el sistema.</p>
            <p>Nuestro equipo técnico ha sido notificado y está trabajando para resolver el problema.</p>
        </div>
        <div class="error-actions">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home"></i>
                Ir al Dashboard
            </a>
            <a href="javascript:location.reload()" class="btn btn-secondary">
                <i class="fas fa-refresh"></i>
                Intentar de nuevo
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
    font-size: 6rem;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    animation: shake 0.82s cubic-bezier(.36,.07,.19,.97) both infinite;
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
    color: #f5576c;
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

@keyframes shake {
    10%, 90% {
        transform: translate3d(-1px, 0, 0);
    }
    
    20%, 80% {
        transform: translate3d(2px, 0, 0);
    }

    30%, 50%, 70% {
        transform: translate3d(-4px, 0, 0);
    }

    40%, 60% {
        transform: translate3d(4px, 0, 0);
    }
}

@media (max-width: 768px) {
    .error-code h1 {
        font-size: 4rem;
    }
    
    .error-message h2 {
        font-size: 2rem;
    }
    
    .error-message p {
        font-size: 1rem;
    }
}
</style>
@endsection
