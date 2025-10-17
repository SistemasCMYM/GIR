{{-- resources/views/auth/credentials.blade.php --}}

<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Credenciales - GIR-365</title>
    <meta name="description" content="Ingreso de credenciales - Sistema GIR-365">
    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        :root {
            --gir-primary: #1a1a1a;
            --gir-secondary: #D1A854;
            --gir-accent: #847D77;
            --gir-success: #10b981;
            --gir-danger: #ef4444;
            --gir-warning: #f59e0b;
            --gir-info: #3b82f6;
            --gir-light: #f8fafc;
            --gir-dark: #0f172a;
            --gir-white: #ffffff;
            --gir-font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --gir-font-size-xs: clamp(8px, 0.7vw, 10px);
            --gir-font-size-sm: clamp(10px, 0.9vw, 12px);
            --gir-font-size-base: clamp(12px, 1vw, 14px);
            --gir-font-size-lg: clamp(14px, 1.2vw, 16px);
            --gir-font-size-xl: clamp(16px, 1.4vw, 18px);
            --gir-font-size-2xl: clamp(18px, 1.8vw, 22px);
            --gir-font-size-3xl: clamp(20px, 2.2vw, 26px);
            --gir-font-size-4xl: clamp(26px, 3vw, 40px);
            --gir-border-radius: 12px;
            --gir-border-radius-lg: 16px;
            --gir-border-radius-xl: 20px;
            --gir-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --gir-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
            --gir-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
            --gir-shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.25);
            --gir-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --gir-gradient-primary: linear-gradient(135deg, #D1A854 0%, #EDC979 50%, #D1A854 100%);
            --gir-gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            --gir-gradient-hero: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: var(--gir-font-family);
            font-size: var(--gir-font-size-base);
            line-height: 1.6;
            color: var(--gir-dark);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: var(--gir-gradient-hero);
            overflow-x: hidden;
        }

        .auth-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            /* Imagen de fondo responsiva */
            /* background: url('{{ asset('img/Element_Graphic_3840x2160.mp4') }}') no-repeat center center; */
            background-size: cover;
            background-attachment: fixed;
            /* opcional: efecto parallax en pantallas grandes */
        }

        .auth-layout::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            /* Capa superpuesta semitransparente para mejor contraste */
            background-color: rgba(255, 255, 255, 0.6);
            opacity: 0.5;
            z-index: -1;
        }

        .auth-card {
            background: white;
            border-radius: var(--gir-border-radius-xl);
            box-shadow: var(--gir-shadow-xl);
            overflow: visible;
            width: 100%;
            max-width: 480px;
            position: relative;
            /* Altura adaptativa para contenido completo */
            min-height: 500px;
            /* Smartwatch: altura suficiente para contenido */
            height: auto;
        }

        @media (max-width: 320px) {
            .auth-card {
                min-height: 480px;
                /* Smartwatch: altura mínima que muestre todo */
                max-height: 90vh;
                /* Smartwatch: máximo 90% del viewport */
            }
        }

        @media (min-width: 321px) and (max-width: 767px) {
            .auth-card {
                min-height: 520px;
                /* Mobile: altura mínima */
                max-height: 85vh;
                /* Mobile: máximo 85% del viewport */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-card {
                min-height: 580px;
                /* Foldable: altura mínima */
                max-height: 720px;
                /* Foldable: altura máxima controlada */
            }
        }

        @media (min-width: 1025px) and (max-width: 3839px) {
            .auth-card {
                min-height: 620px;
                /* Desktop: altura mínima */
                max-height: 750px;
                /* Desktop: altura máxima controlada */
            }
        }

        @media (min-width: 3840px) {
            .auth-card {
                min-height: 1200px;
                /* 4K: altura mínima */
                max-height: 1920px;
                /* 4K: altura máxima */
            }
        }

        .auth-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--gir-gradient-primary);
        }

        .auth-header {
            padding: 1rem 2.5rem 0.5rem; /* Más compacto */
            text-align: center;
            background: white;
        }

        @media (max-width: 320px) {
            .auth-header {
                padding: 0.6rem 1rem 0.3rem; /* Smartwatch ultra-compacto */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-header {
                padding: 1.2rem 2.5rem 0.6rem; /* Foldable compacto */
            }
        }

        @media (min-width: 3840px) {
            .auth-header {
                padding: 2rem 4rem 1.5rem; /* 4K proporcional */
            }
        }

        .auth-logo {
            width: 50px;
            /* Reducido de 80px */
            height: 50px;
            /* Reducido de 80px */
            background: var(--gir-gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            /* Reducido de 1.5rem */
            color: white;
            font-size: 20px;
            /* Reducido de 32px */
            box-shadow: var(--gir-shadow-lg);
        }

        @media (max-width: 320px) {
            .auth-logo {
                width: 35px;
                /* Smartwatch ultra-compacto */
                height: 35px;
                font-size: 16px;
                margin: 0 auto 0.5rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-logo {
                width: 55px;
                /* Foldable */
                height: 55px;
                font-size: 22px;
            }
        }

        @media (min-width: 3840px) {
            .auth-logo {
                width: 80px;
                /* 4K - tamaño original */
                height: 80px;
                font-size: 32px;
            }
        }

        .auth-title {
            font-size: clamp(18px, 4vw, 30px); /* Reducido significativamente */
            font-weight: 800;
            color: var(--gir-dark);
            margin-bottom: 0.25rem; /* Reducido margen */
            line-height: 1.1; /* Línea más compacta */
        }

        .auth-subtitle {
            font-size: clamp(12px, 2.5vw, 16px); /* Reducido significativamente */
            color: #64748b;
            margin: 0;
            font-weight: 500;
            line-height: 1.2;
        }

        .auth-body {
            padding: 0 2.5rem 1rem; /* Más compacto */
        }

        @media (max-width: 320px) {
            .auth-body {
                padding: 0 1rem 0.6rem; /* Smartwatch ultra-compacto */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-body {
                padding: 0 2.5rem 1.2rem; /* Foldable compacto */
            }
        }

        @media (min-width: 3840px) {
            .auth-body {
                padding: 0 4rem 2rem; /* 4K proporcional */
            }
        }

        .bg-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }

        .empresa-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 0.75rem;
            /* Reducido de 1rem */
            margin-bottom: 1rem;
            /* Reducido de 1.5rem */
            border-left: 4px solid var(--gir-secondary);
        }

        @media (max-width: 320px) {
            .empresa-info {
                padding: 0.5rem;
                /* Smartwatch ultra-compacto */
                margin-bottom: 0.75rem;
                font-size: var(--gir-font-size-xs);
            }
        }

        @media (min-width: 3840px) {
            .empresa-info {
                padding: 1rem;
                /* 4K - tamaño original */
                margin-bottom: 1.5rem;
            }
        }

        .form-group {
            margin-bottom: 1rem;
            /* Reducido de 1.5rem */
        }

        @media (max-width: 320px) {
            .form-group {
                margin-bottom: 0.75rem;
                /* Smartwatch ultra-compacto */
            }
        }

        .form-label-modern {
            font-weight: 600;
            font-size: clamp(10px, 2vw, 14px); /* Tamaño responsive más pequeño */
            color: var(--gir-dark);
            margin-bottom: 0.35rem; /* Reducido margen */
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.3px; /* Reducido espaciado */
        }

        @media (max-width: 320px) {
            .form-label-modern {
                font-size: 9px; /* Ultra compacto para smartwatch */
                margin-bottom: 0.2rem;
                letter-spacing: 0.1px;
            }
        }

        .form-control-modern {
            width: 100%;
            padding: 0.6rem 0.8rem; /* Reducido de 0.75rem 1rem */
            font-size: clamp(10px, 2vw, 13px); /* Reducido significativamente */
            border: 2px solid #e2e8f0;
            border-radius: var(--gir-border-radius);
            background: white;
            transition: var(--gir-transition);
            color: var(--gir-dark);
        }

        @media (max-width: 320px) {
            .form-control-modern {
                padding: 0.4rem 0.6rem; /* Smartwatch ultra-compacto */
                font-size: 9px;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .form-control-modern {
                padding: 0.65rem 0.85rem; /* Foldable */
                font-size: 12px;
            }
        }

        @media (min-width: 3840px) {
            .form-control-modern {
                padding: 1rem 1.25rem; /* 4K - tamaño original */
                font-size: var(--gir-font-size-base);
            }
        }

        .form-control-modern:focus {
            outline: none;
            border-color: var(--gir-secondary);
            box-shadow: 0 0 0 0.2rem rgba(209, 168, 84, 0.25);
            transform: translateY(-1px);
        }

        .form-control-modern::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .form-text-modern {
            font-size: var(--gir-font-size-sm);
            color: #64748b;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-modern {
            width: 100%;
            padding: 0.6rem 1.2rem; /* Reducido de 0.75rem 1.5rem */
            background: var(--gir-gradient-primary);
            border: none;
            border-radius: var(--gir-border-radius);
            color: white;
            font-size: clamp(10px, 2.2vw, 14px); /* Reducido significativamente */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px; /* Reducido espaciado */
            transition: var(--gir-transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            box-shadow: var(--gir-shadow-md);
        }

        .btn-primary-modern:disabled {
            opacity: 0.8;
            cursor: not-allowed;
            transform: none;
            background: var(--gir-accent);
        }

        .btn-primary-modern.loading {
            background: var(--gir-accent);
            cursor: not-allowed;
            pointer-events: none;
        }

        .btn-text {
            transition: opacity 0.3s ease;
        }

        .btn-loading-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            font-size: clamp(8px, 2vw, 12px); /* Texto más pequeño para la validación */
        }

        .btn-primary-modern.loading .btn-text {
            opacity: 0;
        }

        .btn-primary-modern.loading .btn-loading-text {
            opacity: 1;
        }

        .loading-spinner {
            display: inline-block;
            width: clamp(12px, 2.5vw, 16px);
            height: clamp(12px, 2.5vw, 16px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 320px) {
            .btn-primary-modern {
                padding: 0.4rem 0.8rem; /* Smartwatch ultra-compacto */
                font-size: 9px;
                letter-spacing: 0.1px;
            }
            
            .btn-loading-text {
                font-size: 7px; /* Texto ultra compacto para smartwatch */
            }
            
            .loading-spinner {
                width: 10px;
                height: 10px;
                border-width: 1px;
                margin-right: 0.25rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .btn-primary-modern {
                padding: 0.65rem 1.3rem; /* Foldable */
                font-size: 12px;
            }
            
            .btn-loading-text {
                font-size: 10px;
            }
        }

        @media (min-width: 3840px) {
            .btn-primary-modern {
                padding: 1rem 2rem; /* 4K - tamaño original */
                font-size: var(--gir-font-size-lg);
            }
            
            .btn-loading-text {
                font-size: var(--gir-font-size-base);
            }
            
            .loading-spinner {
                width: 20px;
                height: 20px;
                border-width: 3px;
            }
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--gir-shadow-lg);
        }

        .btn-primary-modern:active {
            transform: translateY(0);
        }

        .btn-primary-modern:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-link-modern {
            color: var(--gir-secondary);
            text-decoration: none;
            font-weight: 600;
            font-size: var(--gir-font-size-sm);
            transition: var(--gir-transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-link-modern:hover {
            color: var(--gir-accent);
            text-decoration: none;
        }

        .alert-modern {
            border: none;
            border-radius: var(--gir-border-radius);
            padding: 0.75rem 1rem;
            /* Reducido de 1rem 1.25rem */
            margin-bottom: 1rem;
            /* Reducido de 1.5rem */
            font-size: var(--gir-font-size-xs);
            /* Reducido de sm */
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 320px) {
            .alert-modern {
                padding: 0.5rem 0.75rem;
                /* Smartwatch ultra-compacto */
                margin-bottom: 0.75rem;
                font-size: 10px;
            }
        }

        @media (min-width: 3840px) {
            .alert-modern {
                padding: 1rem 1.25rem;
                /* 4K - tamaño original */
                margin-bottom: 1.5rem;
                font-size: var(--gir-font-size-sm);
            }
        }

        .alert-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-danger-modern {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-danger-modern::before {
            background: var(--gir-danger);
        }

        .alert-success-modern {
            background: #d1fae5;
            color: #065f46;
        }

        .alert-success-modern::before {
            background: var(--gir-success);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar personalizado para dispositivos compactos */
        .tw-scrollbar-thin::-webkit-scrollbar,
        .auth-body::-webkit-scrollbar {
            width: 4px;
        }

        .tw-scrollbar-thin::-webkit-scrollbar-track,
        .auth-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .tw-scrollbar-thin::-webkit-scrollbar-thumb,
        .auth-body::-webkit-scrollbar-thumb {
            background: var(--gir-secondary);
            border-radius: 10px;
        }

        .tw-scrollbar-thin::-webkit-scrollbar-thumb:hover,
        .auth-body::-webkit-scrollbar-thumb:hover {
            background: var(--gir-accent);
        }

        @media (max-width: 768px) {
            .auth-layout {
                padding: 1rem;
            }

            .auth-header {
                padding: 1.25rem 2rem 0.75rem;
                /* Más compacto */
            }

            .auth-body {
                padding: 0 2rem 1.25rem;
                /* Más compacto */
            }

            .auth-title {
                font-size: var(--gir-font-size-xl);
                /* Reducido de 2xl */
            }
        }

        @media (max-width: 480px) {
            .auth-header {
                padding: 1rem 1.5rem 0.5rem;
                /* Más compacto */
            }

            .auth-body {
                padding: 0 1.5rem 1rem;
                /* Más compacto */
            }

            .form-control-modern {
                padding: 0.625rem 0.875rem;
                /* Más compacto */
            }

            .btn-primary-modern {
                padding: 0.625rem 1.25rem;
                /* Más compacto */
            }
        }
    </style>
</head>

<body>
    <div class="auth-layout">
        <video autoplay muted loop playsinline class="bg-video">
            <source src="{{ asset('img/Element_Graphic_3840x2160.mp4') }}" type="video/mp4">
            Tu navegador no soporta videos HTML5.
        </video>


        <div class="auth-card fade-in" data-aos="fade-up" data-aos-duration="600">
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="auth-title">Ingreso de Credenciales</h1>
                <p class="auth-subtitle">Ingrese su correo y contraseña para acceder a la plataforma</p>
            </div>
            <div class="auth-body" style="overflow-y: auto; max-height: calc(100vh - 140px); padding-right: 6px;">
                <div class="empresa-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-building text-primary me-2 d-none d-sm-inline"></i>
                        <div class="w-100">
                            <small class="text-muted d-block" style="font-size: clamp(8px, 1.5vw, 10px);">Empresa:</small>
                            <div class="fw-bold text-truncate" style="font-size: clamp(10px, 2vw, 13px);">
                                {{ $empresa['razon_social'] ?? 'Empresa no identificada' }}
                            </div>
                            @if (isset($empresa['nit']))
                                <small class="text-muted d-none d-sm-block" style="font-size: clamp(8px, 1.5vw, 10px);">NIT: {{ $empresa['nit'] }}</small>
                            @endif
                        </div>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="alert-modern alert-danger-modern" data-aos="shake">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('login.credentials.verify') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label-modern">
                            <i class="fas fa-envelope me-1"></i> Correo Electrónico
                        </label>
                        <input type="email" class="form-control-modern @error('email') is-invalid @enderror"
                            id="email" name="email" value="{{ old('email') }}" required autofocus
                            placeholder="ejemplo@empresa.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label-modern">
                            <i class="fas fa-lock me-1"></i> Contraseña
                        </label>
                        <input type="password" class="form-control-modern @error('password') is-invalid @enderror"
                            id="password" name="password" required placeholder="••••••••">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary-modern" id="loginBtn">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                        </span>
                        <span class="btn-loading-text">
                            <span class="loading-spinner"></span> Validando Credenciales
                        </span>
                    </button>
                    <a href="{{ route('login.nit') }}" class="btn-link-modern mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Cambiar Empresa
                    </a>
                </form>
                <div class="text-center mt-3">
                    <small class="text-muted" style="font-size: clamp(8px, 1.8vw, 11px);">
                        <span class="d-none d-sm-inline">¿Problemas para acceder? </span>
                        <a href="#" class="btn-link-modern">Contacte al administrador</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                duration: 600,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });
            
            // Focus en el campo email
            document.getElementById('email').focus();
            
            // Animación del botón de login
            const loginForm = document.querySelector('form');
            const loginBtn = document.getElementById('loginBtn');
            
            loginForm.addEventListener('submit', function(e) {
                // Validar que los campos no estén vacíos
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value.trim();
                
                if (email && password) {
                    // Activar estado de carga
                    loginBtn.classList.add('loading');
                    loginBtn.disabled = true;
                    
                    // Simular un pequeño delay para mostrar la animación
                    setTimeout(() => {
                        // El formulario se enviará normalmente después del delay
                        // No es necesario hacer nada más aquí ya que el formulario se envía automáticamente
                    }, 100);
                }
                // Si los campos están vacíos, el formulario no se enviará y se mostrarán los errores de validación
            });
            
            // Reactivar el botón si hay errores de validación del servidor
            window.addEventListener('pageshow', function() {
                if (loginBtn.classList.contains('loading')) {
                    loginBtn.classList.remove('loading');
                    loginBtn.disabled = false;
                }
            });
        });
    </script>
</body>

</html>
