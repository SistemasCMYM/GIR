<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIR-365 - Plataforma Integral de Gestión de Riesgos Empresariales</title>
    <meta name="description" content="Sistema integral para la evaluación y gestión de riesgos psicosociales, hallazgos de seguridad y salud ocupacional basado en normativas del Ministerio de la Protección Social.">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Modern Landing Styles -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    
    {{-- Scripts antes del cierre del body --}}
    <script src="{{ asset('js/app.js') }}" defer></script>
    
    <style>
        :root {
            /* GIR-365 Brand Colors */
            --gir-primary: #1a1a1a;
            --gir-secondary: #D1A854;
            --gir-accent: #847D77;
            --gir-success: #10b981;
            --gir-danger: #ef4444;
            --gir-warning: #f59e0b;
            --gir-dark: #0f0f0f;
            --gir-light: #ffffff;
            --gir-text-primary: #ffffff;
            --gir-text-secondary: rgba(255, 255, 255, 0.8);
            --gir-text-muted: rgba(255, 255, 255, 0.6);
            
            /* Modern Gradients */
            --gir-gradient-primary: linear-gradient(135deg, #D1A854 0%, #847D77 50%, #D1A854 100%);
            --gir-gradient-dark: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            --gir-gradient-accent: linear-gradient(135deg, #847D77 0%, #D1A854 100%);
            
            /* Responsive Typography */
            --gir-font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --gir-font-size-base: clamp(14px, 1.2vw, 18px);
            --gir-font-size-sm: clamp(12px, 1vw, 16px);
            --gir-font-size-lg: clamp(18px, 1.5vw, 24px);
            --gir-font-size-xl: clamp(24px, 2.5vw, 48px);
            --gir-font-size-xxl: clamp(32px, 4vw, 72px);
            
            /* Shadows & Effects */
            --gir-shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --gir-shadow-md: 0 4px 12px rgba(0, 0, 0, 0.15);
            --gir-shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.2);
            --gir-shadow-xl: 0 16px 48px rgba(0, 0, 0, 0.25);
            
            /* 4K Ultra HD Breakpoints */
            --breakpoint-4k: 3840px;
            --breakpoint-foldable: 720px;
            --breakpoint-smartwatch: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: var(--gir-font-family);
            font-size: var(--gir-font-size-base);
            line-height: 1.6;
            color: var(--gir-text-primary);
            background: var(--gir-primary);
            overflow-x: hidden;
            position: relative;
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--gir-gradient-primary);
            background-size: 400% 400%;
            animation: gradientShift 3s ease-in-out infinite;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
        }

        .loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loading-logo {
            width: clamp(80px, 15vw, 200px);
            height: clamp(80px, 15vw, 200px);
            background: var(--gir-light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: clamp(24px, 5vw, 64px);
            color: var(--gir-primary);
            font-weight: 800;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
            box-shadow: var(--gir-shadow-xl);
        }

        .loading-text {
            font-size: var(--gir-font-size-xl);
            font-weight: 700;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .loading-bar {
            width: 200px;
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 2px;
            overflow: hidden;
        }

        .loading-progress {
            height: 100%;
            background: var(--gir-light);
            border-radius: 2px;
            width: 0%;
            animation: loadingProgress 2s ease-out forwards;
        }

        /* Modern Navigation */
        .navbar-modern {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            transition: all 0.3s ease;
            transform: translateY(-100%);
            opacity: 0;
        }

        .navbar-modern.visible {
            transform: translateY(0);
            opacity: 1;
        }

        .navbar-brand-modern {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: var(--gir-text-primary);
            font-weight: 700;
            font-size: var(--gir-font-size-lg);
        }

        .navbar-brand-modern:hover {
            color: var(--gir-secondary);
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            background: var(--gir-gradient-accent);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 20px;
            color: var(--gir-light);
        }

        .nav-links {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2rem;
        }

        .nav-link-modern {
            color: var(--gir-text-secondary);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link-modern::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gir-secondary);
            transition: width 0.3s ease;
        }

        .nav-link-modern:hover {
            color: var(--gir-text-primary);
        }

        .nav-link-modern:hover::after {
            width: 100%;
        }

        .btn-login {
            background: var(--gir-gradient-accent);
            color: var(--gir-light);
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--gir-shadow-md);
        }

        .btn-login:hover {
            color: var(--gir-light);
            transform: translateY(-2px);
            box-shadow: var(--gir-shadow-lg);
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: var(--gir-primary);
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(209, 168, 84, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(132, 125, 119, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            transform: translateY(50px);
            opacity: 0;
            animation: fadeInUp 1s ease-out 1s both;
        }

        .hero-title {
            font-size: var(--gir-font-size-xxl);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 2rem;
            color: var(--gir-text-primary);
        }

        .hero-subtitle {
            font-size: var(--gir-font-size-lg);
            color: var(--gir-text-secondary);
            margin-bottom: 3rem;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: var(--gir-gradient-accent);
            color: var(--gir-light);
            border: none;
            padding: 16px 32px;
            border-radius: 16px;
            font-size: var(--gir-font-size-base);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: var(--gir-shadow-lg);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-primary:hover {
            color: var(--gir-light);
            transform: translateY(-4px);
            box-shadow: var(--gir-shadow-xl);
        }

        .btn-hero-secondary {
            background: transparent;
            color: var(--gir-text-primary);
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 14px 30px;
            border-radius: 16px;
            font-size: var(--gir-font-size-base);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-hero-secondary:hover {
            color: var(--gir-light);
            border-color: var(--gir-secondary);
            background: rgba(209, 168, 84, 0.1);
        }

        .hero-visual {
            position: relative;
            transform: translateY(30px);
            opacity: 0;
            animation: fadeInUp 1s ease-out 1.2s both;
        }

        .hero-image {
            max-width: 100%;
            height: auto;
            border-radius: 24px;
            box-shadow: var(--gir-shadow-xl);
            background: var(--gir-gradient-dark);
            padding: 2rem;
        }

        .floating-card {
            position: absolute;
            background: var(--gir-light);
            color: var(--gir-primary);
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--gir-shadow-lg);
            animation: float 3s ease-in-out infinite;
        }

        .floating-card.card-1 {
            top: 10%;
            right: -10%;
            animation-delay: 0s;
        }

        .floating-card.card-2 {
            bottom: 20%;
            left: -10%;
            animation-delay: 1s;
        }

        /* Features Section */
        .features-section {
            padding: 8rem 0;
            background: var(--gir-dark);
            position: relative;
        }

        .section-title {
            font-size: var(--gir-font-size-xl);
            font-weight: 700;
            text-align: center;
            margin-bottom: 4rem;
            color: var(--gir-text-primary);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            transition: all 0.3s ease;
            height: 100%;
            backdrop-filter: blur(10px);
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: var(--gir-secondary);
            box-shadow: var(--gir-shadow-xl);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--gir-gradient-accent);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 32px;
            color: var(--gir-light);
        }

        .feature-title {
            font-size: var(--gir-font-size-lg);
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--gir-text-primary);
        }

        .feature-description {
            color: var(--gir-text-secondary);
            line-height: 1.6;
        }

        /* Modules Section */
        .modules-section {
            padding: 8rem 0;
            background: var(--gir-primary);
        }

        .module-card {
            background: var(--gir-light);
            color: var(--gir-primary);
            border-radius: 24px;
            padding: 3rem;
            margin-bottom: 2rem;
            box-shadow: var(--gir-shadow-lg);
            transition: all 0.3s ease;
        }

        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--gir-shadow-xl);
        }

        .module-icon {
            width: 64px;
            height: 64px;
            background: var(--gir-gradient-accent);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            font-size: 24px;
            color: var(--gir-light);
        }

        .module-title {
            font-size: var(--gir-font-size-lg);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .module-description {
            color: var(--gir-accent);
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .module-features {
            list-style: none;
            padding: 0;
        }

        .module-features li {
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(132, 125, 119, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .module-features li:last-child {
            border-bottom: none;
        }

        .module-features i {
            color: var(--gir-success);
        }

        /* Contact Section */
        .contact-section {
            padding: 8rem 0;
            background: var(--gir-dark);
            text-align: center;
        }

        .contact-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 4rem;
            backdrop-filter: blur(10px);
            max-width: 600px;
            margin: 0 auto;
        }

        .contact-title {
            font-size: var(--gir-font-size-xl);
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--gir-text-primary);
        }

        .contact-subtitle {
            color: var(--gir-text-secondary);
            margin-bottom: 3rem;
            font-size: var(--gir-font-size-lg);
        }

        .contact-info {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .contact-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            color: var(--gir-text-secondary);
        }

        .contact-item i {
            color: var(--gir-secondary);
            font-size: 20px;
        }

        /* Animations */
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes loadingProgress {
            0% { width: 0%; }
            100% { width: 100%; }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* 4K Ultra HD Responsive Design */
        @media (min-width: 3840px) {
            :root {
                --gir-font-size-base: 24px;
                --gir-font-size-sm: 20px;
                --gir-font-size-lg: 32px;
                --gir-font-size-xl: 48px;
                --gir-font-size-xxl: 96px;
            }
            
            .hero-section {
                padding: 8rem 0;
            }
            
            .features-section,
            .modules-section,
            .contact-section {
                padding: 12rem 0;
            }
            
            .feature-card,
            .module-card {
                padding: 4rem;
            }
            
            .contact-card {
                padding: 6rem;
            }
        }

        /* Foldable Device Support */
        @media (max-width: 720px) and (orientation: portrait) {
            .hero-title {
                font-size: clamp(28px, 8vw, 48px);
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: stretch;
            }
            
            .nav-links {
                display: none;
            }
            
            .floating-card {
                display: none;
            }
        }

        /* Smartwatch Compatibility */
        @media (max-width: 280px) {
            .hero-title {
                font-size: 20px;
                line-height: 1.2;
            }
            
            .hero-subtitle {
                font-size: 14px;
            }
            
            .btn-hero-primary,
            .btn-hero-secondary {
                padding: 12px 16px;
                font-size: 14px;
            }
            
            .feature-card,
            .module-card {
                padding: 1.5rem;
            }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Animation on Scroll */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .animate-on-scroll.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Focus and Accessibility */
        .btn-hero-primary:focus,
        .btn-hero-secondary:focus,
        .btn-login:focus,
        .nav-link-modern:focus {
            outline: 2px solid var(--gir-secondary);
            outline-offset: 2px;
        }

        /* Reduced Motion Support */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    {{-- 
      INTEGRACIÓN GIR 365 - NO MODIFICAR LÓGICA 
      @last_verified: 2025-01-21 
      @security_level: critical
    --}}

    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-logo">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="loading-text">GIR-365</div>
        <div class="loading-bar">
            <div class="loading-progress" id="loadingProgress"></div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar-modern" id="navbar">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center w-100">
                <a href="#" class="navbar-brand-modern">
                    <div class="brand-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span>GIR-365</span>
                </a>
                
                <ul class="nav-links d-none d-lg-flex">
                    <li><a href="#inicio" class="nav-link-modern">Inicio</a></li>
                    <li><a href="#caracteristicas" class="nav-link-modern">Características</a></li>
                    <li><a href="#modulos" class="nav-link-modern">Módulos</a></li>
                    <li><a href="#contacto" class="nav-link-modern">Contacto</a></li>
                </ul>
                
                <a href="{{ route('login.nit') }}" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="inicio">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Gestión Integral de <span style="background: linear-gradient(45deg, #fff, #a7c7e7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Riesgos Empresariales</span>
                        </h1>
                        <p class="hero-subtitle">
                            Plataforma especializada en evaluación psicosocial, gestión de hallazgos y cumplimiento normativo según estándares del Ministerio de la Protección Social y Universidad Javeriana.
                        </p>
                        <div class="hero-buttons">
                            <a href="{{ route('login.nit') }}" class="btn-hero-primary">
                                <i class="fas fa-rocket"></i>
                                Comenzar Ahora
                            </a>
                            <a href="#caracteristicas" class="btn-hero-secondary">
                                <i class="fas fa-play"></i>
                                Ver Demo
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="hero-visual">
                        <div class="hero-image d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-chart-line" style="font-size: 8rem; color: var(--gir-secondary); margin-bottom: 2rem;"></i>
                                <h3 style="color: var(--gir-light); margin-bottom: 1rem;">Inicio Moderno</h3>
                                <p style="color: var(--gir-text-secondary);">Gráficas 3D, análisis en tiempo real y diseño responsivo</p>
                            </div>
                        </div>
                        
                        <!-- Floating Cards -->
                        <div class="floating-card card-1">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-brain me-3" style="color: var(--gir-secondary); font-size: 24px;"></i>
                                <div>
                                    <strong>Evaluación Psicosocial</strong>
                                    <br><small>Análisis completo de riesgos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="floating-card card-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-search me-3" style="color: var(--gir-success); font-size: 24px;"></i>
                                <div>
                                    <strong>Gestión de Hallazgos</strong>
                                    <br><small>Seguimiento detallado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section animate-on-scroll" id="caracteristicas">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Características Principales</h2>
            <div class="row">
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-title">Evaluación Psicosocial</h3>
                        <p class="feature-description">
                            Cuestionarios especializados de Estrés, Factores Extralaborales e Intralaborales según normativas del Ministerio de la Protección Social.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="feature-title">Gestión de Hallazgos</h3>
                        <p class="feature-description">
                            Sistema completo para registro, seguimiento y cierre de hallazgos de seguridad y salud ocupacional con trazabilidad total.
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="feature-title">Reportes Avanzados</h3>
                        <p class="feature-description">
                            Informes diagnósticos detallados con gráficas 3D, análisis estadísticos y visualizaciones interactivas para la toma de decisiones.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="modules-section animate-on-scroll" id="modulos">
        <div class="container">
            <h2 class="section-title" data-aos="fade-up">Módulos de la Plataforma</h2>
            <div class="row">
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="module-title">Módulo Psicosocial</h3>
                        <p class="module-description">
                            Batería completa de Riesgo Psicosocial según parámetros del Ministerio de la Protección Social y Universidad Javeriana.
                        </p>
                        <ul class="module-features">
                            <li><i class="fas fa-check"></i> Cuestionario de Estrés</li>
                            <li><i class="fas fa-check"></i> Factores Extralaborales</li>
                            <li><i class="fas fa-check"></i> Factores Intralaborales Forma A y B</li>
                            <li><i class="fas fa-check"></i> Ficha de Datos Generales</li>
                            <li><i class="fas fa-check"></i> Informe Diagnóstico Completo</li>
                            <li><i class="fas fa-check"></i> Socialización de Resultados</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="module-card">
                        <div class="module-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3 class="module-title">Módulo de Hallazgos</h3>
                        <p class="module-description">
                            Sistema integral para la identificación, documentación y seguimiento de hallazgos de seguridad y salud ocupacional.
                        </p>
                        <ul class="module-features">
                            <li><i class="fas fa-check"></i> Registro detallado de hallazgos</li>
                            <li><i class="fas fa-check"></i> Clasificación por niveles de riesgo</li>
                            <li><i class="fas fa-check"></i> Asignación de responsables</li>
                            <li><i class="fas fa-check"></i> Seguimiento de planes de acción</li>
                            <li><i class="fas fa-check"></i> Evidencias fotográficas</li>
                            <li><i class="fas fa-check"></i> Notificaciones automáticas</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section animate-on-scroll" id="contacto">
        <div class="container">
            <div class="contact-card" data-aos="fade-up">
                <h2 class="contact-title">¿Listo para comenzar?</h2>
                <p class="contact-subtitle">
                    Únete a las empresas que ya confían en GIR-365 para la gestión integral de sus riesgos empresariales.
                </p>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>{{ config('app.contact_email', 'info@tudominio.com') }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-phone"></i>
                        <span>{{ config('app.contact_phone', '+57 (1) 234-5678') }}</span>
                    </div>
                    <div class="contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Bogotá, Colombia</span>
                    </div>
                </div>
                <a href="{{ route('login.nit') }}" class="btn-hero-primary">
                    <i class="fas fa-rocket"></i>
                    Iniciar Sesión
                </a>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-quart',
            once: true
        });

        // Loading screen
        window.addEventListener('load', function() {
            const loadingScreen = document.getElementById('loadingScreen');
            const navbar = document.getElementById('navbar');
            
            setTimeout(() => {
                loadingScreen.classList.add('hidden');
                navbar.classList.add('visible');
            }, 2000);
        });

        // Smooth scrolling para navegación (no bloquear toggles Bootstrap)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href') || '';
                if (this.hasAttribute('data-bs-toggle')) return; // dropdown/tab/collapse/etc.
                if (href === '#' || href === '#!') { e.preventDefault(); return; }
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, true);
        });

        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(26, 26, 26, 0.98)';
            } else {
                navbar.style.background = 'rgba(26, 26, 26, 0.95)';
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe all elements with animation class
        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
