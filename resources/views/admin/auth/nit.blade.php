{{--
  FORMULARIO NIT - PASO 1 AUTENTICACIÓN GIR-365
  Interfaz moderna para validación de empresa por NIT
  @last_verified: 2025-01-21
  @security_level: critical
--}}
<!DOCTYPE html>
<html lang="es" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Identificación de Empresa - GIR-365</title>
    <meta name="description" content="Identificación de empresa mediante NIT - Sistema GIR-365">

    <!-- Modern CSS Framework -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <style>
        {{-- INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA --}} {{-- @last_verified: 2025-01-21 --}} {{-- @security_level: critical --}} 
        :root {
            /* GIR-365 Brand System */
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

            /* Typography */
            --gir-font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --gir-font-size-xs: clamp(10px, 0.8vw, 12px);
            --gir-font-size-sm: clamp(12px, 1vw, 14px);
            --gir-font-size-base: clamp(14px, 1.2vw, 16px);
            --gir-font-size-lg: clamp(16px, 1.4vw, 18px);
            --gir-font-size-xl: clamp(18px, 1.6vw, 20px);
            --gir-font-size-2xl: clamp(20px, 2vw, 24px);
            --gir-font-size-3xl: clamp(24px, 2.5vw, 30px);
            --gir-font-size-4xl: clamp(30px, 3.5vw, 48px);

            /* Effects */
            --gir-border-radius: 12px;
            --gir-border-radius-lg: 16px;
            --gir-border-radius-xl: 20px;
            --gir-shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --gir-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
            --gir-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
            --gir-shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.25);
            --gir-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

            /* Gradients */
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
            overflow-x: hidden;
        }

        /* Auth Layout */
        .auth-layout {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            position: relative;
            /* Imagen de fondo responsiva */
            background: url('{{ asset('img/frente-edificio-moderno.jpg') }}') no-repeat center center;
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

        /* Auth Card */
        .auth-card {
            background: white;
            border-radius: var(--gir-border-radius-xl);
            box-shadow: var(--gir-shadow-xl);
            overflow: visible; /* Cambiado de hidden a visible */
            width: 100%;
            max-width: 480px;
            position: relative;
            /* Altura adaptativa responsiva para garantizar que el contenido sea visible */
            min-height: 280px; /* Smartwatch: altura mínima necesaria */
            max-height: 280px; /* Smartwatch: dentro del límite requerido */
        }

        @media (min-width: 321px) {
            .auth-card {
                min-height: 400px; /* Mobile: altura mínima */
                max-height: 450px; /* Mobile compacto pero funcional */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-card {
                min-height: 500px; /* Foldable: altura mínima */
                max-height: 720px; /* Foldable: cumple el requerimiento exacto */
            }
        }

        @media (min-width: 1025px) {
            .auth-card {
                min-height: 550px; /* Desktop: altura mínima */
                max-height: 650px; /* Desktop compacto pero usable */
            }
        }

        @media (min-width: 3840px) {
            .auth-card {
                min-height: 1000px; /* 4K: altura mínima */
                max-height: 3840px; /* 4K: cumple el requerimiento exacto */
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
            padding: 1.5rem 3rem 1rem; /* Reducido de 3rem 3rem 2rem */
            text-align: center;
            background: white;
        }

        @media (max-width: 320px) {
            .auth-header {
                padding: 1rem 1.5rem 0.75rem; /* Smartwatch ultra-compacto */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-header {
                padding: 1.75rem 3rem 1.25rem; /* Foldable compacto */
            }
        }

        @media (min-width: 3840px) {
            .auth-header {
                padding: 2rem 4rem 1.5rem; /* 4K proporcional */
            }
        }

        .auth-logo {
            width: 50px; /* Reducido de 80px */
            height: 50px; /* Reducido de 80px */
            background: var(--gir-gradient-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem; /* Reducido de 1.5rem */
            color: white;
            font-size: 20px; /* Reducido de 32px */
            box-shadow: var(--gir-shadow-lg);
        }

        @media (max-width: 320px) {
            .auth-logo {
                width: 35px; /* Smartwatch ultra-compacto */
                height: 35px;
                font-size: 16px;
                margin: 0 auto 0.5rem;
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-logo {
                width: 55px; /* Foldable */
                height: 55px;
                font-size: 22px;
            }
        }

        @media (min-width: 3840px) {
            .auth-logo {
                width: 80px; /* 4K - tamaño original */
                height: 80px;
                font-size: 32px;
            }
        }

        .auth-title {
            font-size: var(--gir-font-size-3xl);
            font-weight: 800;
            color: var(--gir-dark);
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .auth-subtitle {
            font-size: var(--gir-font-size-lg);
            color: #64748b;
            margin: 0;
            font-weight: 500;
        }

        .auth-body {
            padding: 0 3rem 1.5rem; /* Reducido de 0 3rem 3rem */
        }

        @media (max-width: 320px) {
            .auth-body {
                padding: 0 1.5rem 1rem; /* Smartwatch ultra-compacto */
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .auth-body {
                padding: 0 3rem 1.75rem; /* Foldable compacto */
            }
        }

        @media (min-width: 3840px) {
            .auth-body {
                padding: 0 4rem 2rem; /* 4K proporcional */
            }
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1rem; /* Reducido de 1.5rem */
        }

        @media (max-width: 320px) {
            .form-group {
                margin-bottom: 0.75rem; /* Smartwatch ultra-compacto */
            }
        }

        .form-label-modern {
            font-weight: 600;
            font-size: var(--gir-font-size-sm);
            color: var(--gir-dark);
            margin-bottom: 0.5rem; /* Reducido pero no extremo */
            display: block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        @media (max-width: 320px) {
            .form-label-modern {
                font-size: var(--gir-font-size-xs);
                margin-bottom: 0.25rem; /* Smartwatch ultra-compacto */
            }
        }

        .form-control-modern {
            width: 100%;
            padding: 0.75rem 1rem; /* Reducido de 1rem 1.25rem */
            font-size: var(--gir-font-size-sm); /* Reducido de base */
            border: 2px solid #e2e8f0;
            border-radius: var(--gir-border-radius);
            background: white;
            transition: var(--gir-transition);
            color: var(--gir-dark);
        }

        @media (max-width: 320px) {
            .form-control-modern {
                padding: 0.5rem 0.75rem; /* Smartwatch ultra-compacto */
                font-size: var(--gir-font-size-xs);
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .form-control-modern {
                padding: 0.8rem 1.1rem; /* Foldable */
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

        /* Button Styles */
        .btn-primary-modern {
            width: 100%;
            padding: 0.75rem 1.5rem; /* Reducido de 1rem 2rem */
            background: var(--gir-gradient-primary);
            border: none;
            border-radius: var(--gir-border-radius);
            color: white;
            font-size: var(--gir-font-size-base); /* Reducido de lg */
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--gir-transition);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            box-shadow: var(--gir-shadow-md);
        }

        @media (max-width: 320px) {
            .btn-primary-modern {
                padding: 0.5rem 1rem; /* Smartwatch ultra-compacto */
                font-size: var(--gir-font-size-sm);
            }
        }

        @media (min-width: 768px) and (max-width: 1024px) {
            .btn-primary-modern {
                padding: 0.8rem 1.6rem; /* Foldable */
            }
        }

        @media (min-width: 3840px) {
            .btn-primary-modern {
                padding: 1rem 2rem; /* 4K - tamaño original */
                font-size: var(--gir-font-size-lg);
            }
        }

        .btn-primary-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--gir-transition);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--gir-shadow-lg);
        }

        .btn-primary-modern:hover::before {
            left: 100%;
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

        /* Alert Styles */
        .alert-modern {
            border: none;
            border-radius: var(--gir-border-radius);
            padding: 0.75rem 1rem; /* Reducido de 1rem 1.25rem */
            margin-bottom: 1rem; /* Reducido de 1.5rem */
            font-size: var(--gir-font-size-xs); /* Reducido de sm */
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        @media (max-width: 320px) {
            .alert-modern {
                padding: 0.5rem 0.75rem; /* Smartwatch ultra-compacto */
                margin-bottom: 0.75rem;
                font-size: 10px;
            }
        }

        @media (min-width: 3840px) {
            .alert-modern {
                padding: 1rem 1.25rem; /* 4K - tamaño original */
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

        /* Loading State */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 0.8s ease-in-out infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Back to Landing */
        .back-to-landing {
            position: fixed;
            top: 2rem;
            left: 2rem;
            z-index: 1000;
        }

        .btn-back {
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: var(--gir-border-radius-lg);
            color: var(--gir-dark);
            padding: 0.75rem 1.5rem;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            transition: var(--gir-transition);
            box-shadow: var(--gir-shadow-sm);
        }

        .btn-back:hover {
            border-color: var(--gir-secondary);
            color: var(--gir-secondary);
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: var(--gir-shadow-md);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .auth-layout {
                padding: 1rem;
            }

            .auth-header {
                padding: 1.25rem 2rem 0.75rem; /* Más compacto */
            }

            .auth-body {
                padding: 0 2rem 1.25rem; /* Más compacto */
            }

            .auth-title {
                font-size: var(--gir-font-size-xl); /* Reducido de 2xl */
            }

            .back-to-landing {
                top: 1rem;
                left: 1rem;
            }
        }

        @media (max-width: 480px) {
            .auth-header {
                padding: 1rem 1.5rem 0.5rem; /* Más compacto */
            }

            .auth-body {
                padding: 0 1.5rem 1rem; /* Más compacto */
            }

            .form-control-modern {
                padding: 0.625rem 0.875rem; /* Más compacto */
            }

            .btn-primary-modern {
                padding: 0.625rem 1.25rem; /* Más compacto */
            }
        }

        /* 4K Ultra HD Support */
        @media (min-width: 3840px) {
            .auth-card {
                max-width: 600px;
            }

            .auth-header {
                padding: 4rem 4rem 3rem;
            }

            .auth-body {
                padding: 0 4rem 4rem;
            }

            .auth-logo {
                width: 120px;
                height: 120px;
                font-size: 48px;
            }
        }

        /* Animation Classes */
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
        .auth-body::-webkit-scrollbar {
            width: 4px;
        }

        .auth-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .auth-body::-webkit-scrollbar-thumb {
            background: var(--gir-secondary);
            border-radius: 10px;
        }

        .auth-body::-webkit-scrollbar-thumb:hover {
            background: var(--gir-accent);
        }
    </style>
</head>

<body>
    {{-- 
      INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
      @last_verified: 2025-01-21 
      @security_level: critical
    --}}

    <!-- Back to Landing -->
    <div class="back-to-landing">
        <a href="{{ route('landing') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            <span class="d-none d-sm-inline">Volver al inicio</span>
        </a>
    </div>

    <!-- Auth Layout -->
    <div class="auth-layout">
        <div class="auth-card fade-in" data-aos="fade-up" data-aos-duration="600">
            <!-- Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1 class="auth-title">Identificación de Empresa</h1>
                <p class="auth-subtitle">Ingrese el NIT de su empresa para continuar</p>
            </div>

            <!-- Body -->
            <div class="auth-body" style="overflow-y: auto; max-height: calc(100% - 120px);">
                @if ($errors->any())
                    <div class="alert-modern alert-danger-modern" data-aos="shake">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        @foreach ($errors->all() as $error)
                            {{ $error }}
                        @endforeach
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert-modern alert-danger-modern" data-aos="shake">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert-modern alert-success-modern" data-aos="fade-in">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.nit.verify') }}" id="nitForm">
                    @csrf

                    <div class="form-group">
                        <label for="nit" class="form-label-modern">
                            <i class="fas fa-building me-1"></i>
                            NIT de la Empresa
                        </label>
                        <input type="text" class="form-control-modern" id="nit" name="nit"
                            placeholder="Ejemplo: 900123456" value="{{ old('nit') }}" required
                            autocomplete="username" maxlength="15" pattern="[0-9\-]+" data-aos="fade-up"
                            data-aos-delay="100">
                        <div class="form-text-modern">
                            <i class="fas fa-info-circle"></i>
                            Ingrese el NIT sin puntos, sin dígito de verificación
                        </div>
                    </div>

                    <button type="submit" class="btn-primary-modern" id="submitBtn" data-aos="fade-up"
                        data-aos-delay="200">
                        <span class="btn-text">Continuar</span>
                        <span class="loading-spinner d-none"></span>
                    </button>
                </form>

                <div class="text-center mt-4" data-aos="fade-up" data-aos-delay="300">
                    <p class="mb-2" style="color: #64748b; font-size: var(--gir-font-size-sm);">¿Necesita ayuda?</p>
                    <a href="{{ route('landing') }}" class="btn-link-modern">
                        <i class="fas fa-question-circle"></i>
                        Contactar soporte técnico
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize AOS
            AOS.init({
                duration: 600,
                easing: 'ease-out-cubic',
                once: true,
                offset: 50
            });

            // Form handling
            const form = document.getElementById('nitForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const spinner = submitBtn.querySelector('.loading-spinner');
            const nitInput = document.getElementById('nit');

            // NIT input formatting
            nitInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9\-]/g, '');

                // Auto-format NIT (optional enhancement)
                if (value.length > 0 && !value.includes('-') && value.length >= 9) {
                    const numbers = value.replace('-', '');
                    if (numbers.length === 10) {
                        value = numbers.substring(0, 9) + '-' + numbers.substring(9);
                    }
                }

                e.target.value = value;
            });

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                const nitValue = nitInput.value.trim();

                if (nitValue.length < 5) {
                    e.preventDefault();
                    showError('El NIT debe tener al menos 5 dígitos');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Iniciando Verificación...';
                spinner.classList.remove('d-none');

                // Add visual feedback
                submitBtn.style.opacity = '0.8';
            });

            // Auto-focus on NIT input
            nitInput.focus();

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.location.href = '{{ route('landing') }}';
                }
            });

            // Show error function
            function showError(message) {
                // Remove existing alerts
                const existingAlerts = document.querySelectorAll('.alert-modern');
                existingAlerts.forEach(alert => alert.remove());

                // Create new error alert
                const alert = document.createElement('div');
                alert.className = 'alert-modern alert-danger-modern';
                alert.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${message}`;

                // Insert before form
                form.parentNode.insertBefore(alert, form);

                // Animate in
                AOS.init();
                alert.setAttribute('data-aos', 'shake');
                AOS.refresh();
            }

            // Enhanced form validation
            nitInput.addEventListener('blur', function() {
                const value = this.value.trim();
                if (value && value.length < 4) {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });

            // Remove loading state on page visibility change (in case of back button)
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'visible') {
                    submitBtn.disabled = false;
                    btnText.textContent = 'Continuar';
                    spinner.classList.add('d-none');
                    submitBtn.style.opacity = '1';
                }
            });
        });
    </script>
</body>

</html>
