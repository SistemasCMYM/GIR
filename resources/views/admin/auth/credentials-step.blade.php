<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GIR-365 - Credenciales de Usuario</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Source Sans Pro', sans-serif;
        }
        
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-box {
            width: 450px;
            margin: 0 auto;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            border: none;
        }
        
        .brand-link {
            color: white;
            text-decoration: none;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .brand-link:hover {
            color: #f8f9fa;
            text-decoration: none;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .login-box-msg {
            text-align: center;
            margin-bottom: 2rem;
            color: #6c757d;
            font-size: 1.1rem;
        }
        
        .input-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .input-group-text {
            background: #667eea;
            color: white;
            border: 2px solid #667eea;
            border-radius: 0 10px 10px 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            margin-bottom: 1.5rem;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
            position: relative;
        }
        
        .step.active {
            background: #667eea;
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
        
        .step.inactive {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .step-line {
            width: 50px;
            height: 2px;
            background: #28a745;
        }
        
        .company-info {
            background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid #667eea;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 10;
        }
        
        .password-field {
            position: relative;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .remember-me {
            margin-bottom: 1.5rem;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        
        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 300px;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="login-page">
    <div class="login-box">
        <div class="card card-outline">
            <div class="card-header">
                <a href="{{ url('/') }}" class="brand-link">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <span class="brand-text">GIR-365</span>
                </a>
                <div class="mt-2">
                    <small>Plataforma de Gestión Empresarial</small>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Step Indicator -->
                <div class="step-indicator">
                    <div class="step completed">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step active">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <!-- Company Information -->
                @if(session('company_info'))
                <div class="company-info">
                    <div class="d-flex align-items-center">
                        <div class="mr-3">
                            <i class="fas fa-building fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ session('company_info.razon_social') }}</h6>
                            <small class="text-muted">
                                NIT: {{ session('company_info.nit') }}
                                @if(session('company_info.ciudad'))
                                    | {{ session('company_info.ciudad') }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                @endif
                
                <p class="login-box-msg">
                    <strong>Paso 2:</strong> Ingresa tus credenciales de usuario
                </p>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li><i class="fas fa-exclamation-triangle mr-2"></i>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.credentials.verify') }}" method="post" id="credentialsForm">
                    @csrf
                    
                    <!-- Email Field -->
                    <div class="input-group">
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Correo electrónico" 
                               name="email" 
                               value="{{ old('email') }}" 
                               required 
                               autofocus
                               autocomplete="email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Password Field -->
                    <div class="password-field">
                        <div class="input-group">
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   placeholder="Contraseña" 
                                   name="password" 
                                   required
                                   autocomplete="current-password"
                                   id="passwordInput">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </span>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="remember-me">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember">
                                Recordar mi sesión
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('login.nit') }}" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Atrás
                            </a>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Ingresar
                            </button>
                        </div>
                    </div>
                </form>

                <div class="back-link">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-home mr-2"></i>
                        Volver al inicio
                    </a>
                </div>
                
                <!-- Help Section -->
                <div class="text-center mt-4">
                    <hr>
                    <small class="text-muted">
                        ¿Problemas para acceder? 
                        <a href="#" class="text-primary">Contacta soporte</a>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="sr-only">Cargando...</span>
            </div>
            <h6>Verificando credenciales...</h6>
            <small class="text-muted">Por favor espera mientras validamos tu información</small>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        $(document).ready(function() {
            // Form submission with loading overlay
            $('#credentialsForm').on('submit', function() {
                $('#loadingOverlay').css('display', 'flex');
                $('#submitBtn').prop('disabled', true);
                
                // Add timeout in case of server issues
                setTimeout(function() {
                    $('#loadingOverlay').hide();
                    $('#submitBtn').prop('disabled', false);
                }, 30000); // 30 seconds timeout
            });
            
            // Auto-focus email field if empty
            if ($('#email').val() === '') {
                $('#email').focus();
            }
            
            // Enhanced form validation
            $('#credentialsForm').on('submit', function(e) {
                const email = $('input[name="email"]').val();
                const password = $('input[name="password"]').val();
                
                if (!email || !password) {
                    e.preventDefault();
                    alert('Por favor completa todos los campos requeridos.');
                    $('#loadingOverlay').hide();
                    $('#submitBtn').prop('disabled', false);
                    return false;
                }
                
                // Email validation
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Por favor ingresa un correo electrónico válido.');
                    $('#loadingOverlay').hide();
                    $('#submitBtn').prop('disabled', false);
                    return false;
                }
                
                // Password length validation
                if (password.length < 6) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 6 caracteres.');
                    $('#loadingOverlay').hide();
                    $('#submitBtn').prop('disabled', false);
                    return false;
                }
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Keyboard shortcuts
            $(document).keydown(function(e) {
                // ESC key to go back
                if (e.keyCode === 27) {
                    window.location.href = "{{ route('login.nit') }}";
                }
            });
        });
    </script>
</body>
</html>
