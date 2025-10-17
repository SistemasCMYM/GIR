<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GIR-365 - Plataforma Integral de Gestión de Riesgos Empresariales</title>
    <meta name="description" content="Sistema integral para la evaluación y gestión de riesgos psicosociales, hallazgos de seguridad y salud ocupacional basado en normativas del Ministerio de la Protección Social.">
    
    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #64748b;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #0f172a;
            --light: #f8fafc;
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-accent: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .loading-screen.hidden {
            opacity: 0;
            visibility: hidden;
        }

        .loading-content {
            text-align: center;
            color: white;
        }

        .loading-logo {
            font-size: 4rem;
            margin-bottom: 1rem;
            animation: pulse 2s infinite;
        }

        .loading-text {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .loading-bar {
            width: 300px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            overflow: hidden;
            margin: 0 auto;
        }

        .loading-progress {
            height: 100%;
            background: white;
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        /* Navigation */
        .navbar-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 0;
        }

        .navbar-modern.scrolled {
            background: rgba(255, 255, 255, 0.98);
            padding: 0.5rem 0;
            box-shadow: var(--shadow-lg);
        }

        .navbar-brand-modern {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 800;
            font-size: 1.5rem;
        }

        .navbar-brand-modern:hover {
            color: var(--primary);
        }

        .brand-icon {
            width: 40px;
            height: 40px;
            background: var(--gradient-primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
            margin: 0;
        }

        .nav-link-modern {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link-modern:hover {
            color: var(--primary);
            background: rgba(37, 99, 235, 0.1);
        }

        .btn-login {
            background: var(--gradient-primary);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            box-shadow: var(--shadow-md);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-xl);
            color: white;
        }

        /* Hero Section */
        .hero-section {
            min-height: 100vh;
            background: var(--gradient-primary);
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            padding-top: 80px;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="white" stop-opacity="0.1"/><stop offset="100%" stop-color="white" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .hero-subtitle {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
            margin-bottom: 2rem;
            max-width: 600px;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-hero-primary {
            background: white;
            color: var(--primary);
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-lg);
            border: none;
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            color: var(--primary-dark);
        }

        .btn-hero-secondary {
            background: transparent;
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            color: white;
        }

        .hero-image {
            position: relative;
            z-index: 2;
        }

        .hero-Inicio {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg);
            transition: transform 0.3s ease;
        }

        .hero-Inicio:hover {
            transform: perspective(1000px) rotateY(-2deg) rotateX(2deg);
        }

        /* Features Section */
        .features-section {
            padding: 100px 0;
            background: var(--light);
        }

        .section-title {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .section-title p {
            font-size: 1.125rem;
            color: var(--secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
        }

        .feature-icon.gradient-1 { background: var(--gradient-primary); }
        .feature-icon.gradient-2 { background: var(--gradient-secondary); }
        .feature-icon.gradient-3 { background: var(--gradient-accent); }

        .feature-card h4 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--secondary);
            line-height: 1.6;
        }

        /* Stats Section */
        .stats-section {
            padding: 80px 0;
            background: var(--dark);
            color: white;
        }

        .stat-item {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: var(--gradient-accent);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.8;
            font-weight: 500;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: var(--gradient-primary);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .cta-subtitle {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2.5rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: white;
            padding: 3rem 0 1rem;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h5 {
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            margin-bottom: 0.5rem;
            display: block;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .hero-buttons {
                justify-content: center;
            }
            
            .nav-links {
                display: none;
            }
        }

        /* Animations */
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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

        .fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Loading Screen -->
    <div class="loading-screen" id="loadingScreen">
        <div class="loading-content">
            <div class="loading-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="loading-text">GIR-365</div>
            <div class="loading-bar">
                <div class="loading-progress" id="loadingProgress"></div>
            </div>
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
                                <i class="fas fa-rocket me-2"></i>
                                Comenzar Ahora
                            </a>
                            <a href="#caracteristicas" class="btn-hero-secondary">
                                <i class="fas fa-info-circle me-2"></i>
                                Conocer Más
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="hero-image text-center">
                        <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 800 600' fill='none'><rect width='800' height='600' fill='%23f8fafc' rx='20'/><rect x='50' y='80' width='700' height='60' fill='%23667eea' rx='10'/><rect x='70' y='95' width='30' height='30' fill='white' rx='5'/><rect x='120' y='95' width='100' height='30' fill='white' rx='5'/><rect x='600' y='95' width='80' height='30' fill='white' rx='5'/><rect x='50' y='160' width='200' height='400' fill='%23e2e8f0' rx='10'/><rect x='70' y='180' width='160' height='40' fill='%23667eea' rx='5'/><rect x='70' y='240' width='160' height='30' fill='%23cbd5e1' rx='5'/><rect x='70' y='280' width='160' height='30' fill='%23cbd5e1' rx='5'/><rect x='70' y='320' width='160' height='30' fill='%23cbd5e1' rx='5'/><rect x='270' y='160' width='480' height='200' fill='white' rx='10'/><rect x='290' y='180' width='200' height='30' fill='%23667eea' rx='5'/><rect x='290' y='220' width='440' height='120' fill='%23f1f5f9' rx='5'/><rect x='270' y='380' width='230' height='180' fill='white' rx='10'/><rect x='520' y='380' width='230' height='180' fill='white' rx='10'/></svg>" alt="Inicio Preview" class="hero-Inicio">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="caracteristicas">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Características Principales</h2>
                <p>Solución integral diseñada para cumplir con los más altos estándares de gestión de riesgos empresariales</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="feature-card">
                        <div class="feature-icon gradient-1">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h4>Evaluación Psicosocial</h4>
                        <p>Batería completa de riesgo psicosocial basada en normativas oficiales del Ministerio de la Protección Social y Universidad Javeriana.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <div class="feature-icon gradient-2">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4>Gestión de Hallazgos</h4>
                        <p>Sistema integral para identificación, seguimiento y resolución de hallazgos de seguridad y salud ocupacional.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="feature-card">
                        <div class="feature-icon gradient-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Análisis y Reportes</h4>
                        <p>Generación automática de informes detallados y métricas clave para la toma de decisiones estratégicas.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="feature-card">
                        <div class="feature-icon gradient-1">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Gestión Multi-empresa</h4>
                        <p>Administración centralizada para múltiples empresas con roles y permisos granulares por organización.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="feature-card">
                        <div class="feature-icon gradient-2">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Seguridad Avanzada</h4>
                        <p>Autenticación por NIT empresarial y credenciales de usuario con encriptación de datos de alto nivel.</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                    <div class="feature-card">
                        <div class="feature-icon gradient-3">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <h4>Acceso Multiplataforma</h4>
                        <p>Interfaz responsive y moderna accesible desde cualquier dispositivo con experiencia de usuario optimizada.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modules Section -->
    <section class="stats-section" id="modulos">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2 style="color: white;">Módulos de Evaluación</h2>
                <p style="color: rgba(255,255,255,0.8);">Herramientas especializadas basadas en metodologías científicas validadas</p>
            </div>
            
            <div class="row g-4">
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                        <div class="stat-label">Cuestionario de Estrés</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-label">Factores Extralaborales</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-label">Factores Intralaborales</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="stat-label">Ficha de Datos Generales</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div data-aos="fade-up">
                <h2 class="cta-title">¿Listo para Transformar tu Gestión de Riesgos?</h2>
                <p class="cta-subtitle">
                    Únete a las empresas que ya confían en GIR-365 para mantener entornos laborales seguros y productivos
                </p>
                <a href="{{ route('login.nit') }}" class="btn-hero-primary">
                    <i class="fas fa-rocket me-2"></i>
                    Comenzar Evaluación Gratuita
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contacto">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h5>GIR-365</h5>
                    <p>Plataforma integral de gestión de riesgos empresariales basada en normativas oficiales del Ministerio de la Protección Social.</p>
                </div>
                
                <div class="footer-section">
                    <h5>Módulos</h5>
                    <a href="#">Evaluación Psicosocial</a>
                    <a href="#">Gestión de Hallazgos</a>
                    <a href="#">Reportes y Análisis</a>
                    <a href="#">Configuración</a>
                </div>
                
                <div class="footer-section">
                    <h5>Soporte</h5>
                    <a href="#">Documentación</a>
                    <a href="#">Centro de Ayuda</a>
                    <a href="#">Contactar Soporte</a>
                    <a href="#">Estado del Sistema</a>
                </div>
                
                <div class="footer-section">
                    <h5>Legal</h5>
                    <a href="#">Términos de Uso</a>
                    <a href="#">Política de Privacidad</a>
                    <a href="#">Cumplimiento Normativo</a>
                    <a href="#">Certificaciones</a>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} GIR-365. Todos los derechos reservados. Desarrollado con tecnología de vanguardia.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize loading screen
        let progress = 0;
        const loadingProgress = document.getElementById('loadingProgress');
        const loadingScreen = document.getElementById('loadingScreen');
        
        const updateProgress = () => {
            progress += Math.random() * 15;
            if (progress > 100) progress = 100;
            
            loadingProgress.style.width = progress + '%';
            
            if (progress < 100) {
                setTimeout(updateProgress, 100);
            } else {
                setTimeout(() => {
                    loadingScreen.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                    
                    // Initialize AOS
                    AOS.init({
                        duration: 800,
                        easing: 'ease-in-out',
                        once: true,
                        offset: 100
                    });
                    
                }, 500);
            }
        };
        
        document.addEventListener('DOMContentLoaded', () => {
            document.body.style.overflow = 'hidden';
            setTimeout(updateProgress, 500);
        });
        
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scrolling for navigation links (sin interferir con Bootstrap)
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href') || '';
                if (this.hasAttribute('data-bs-toggle')) return; // dejar a Bootstrap manejar
                if (href === '#' || href === '#!') { e.preventDefault(); return; }
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, true);
        });
        
        // Add loading delay to simulate real loading
        window.addEventListener('load', () => {
            if (progress < 100) {
                progress = 100;
                loadingProgress.style.width = '100%';
            }
        });
    </script>
</body>
</html>
