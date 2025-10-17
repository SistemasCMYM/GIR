<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GIR-365 - Acceso Empresarial</title>

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
        
        .step.inactive {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .step-line {
            width: 50px;
            height: 2px;
            background: #e9ecef;
        }
        
        .company-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
        }
        
        .company-info.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
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
                    <div class="step active">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="step-line"></div>
                    <div class="step inactive">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                
                <p class="login-box-msg">
                    <strong>Paso 1:</strong> Ingresa el NIT de tu empresa para comenzar
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

                <form action="{{ route('login.nit.verify') }}" method="post" id="nitForm">
                    @csrf
                    
                    <div class="input-group">
                        <input type="text" 
                               class="form-control @error('nit') is-invalid @enderror" 
                               placeholder="NIT de la empresa (ej: 900123456-1)" 
                               name="nit" 
                               value="{{ old('nit') }}" 
                               required 
                               autofocus
                               pattern="[0-9]{8,11}-?[0-9]?"
                               title="Ingresa un NIT válido"
                               id="nitInput">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-building"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Company Info Preview -->
                    <div class="company-info" id="companyInfo">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <i class="fas fa-building fa-2x text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1" id="companyName">Nombre de la empresa</h6>
                                <small class="text-muted" id="companyDetails">Detalles de la empresa</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" id="submitBtn">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Continuar al Paso 2
                            </button>
                        </div>
                    </div>
                </form>

                <div class="back-link">
                    <a href="{{ url('/') }}">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        $(document).ready(function() {
            let nitTimeout;
            
            // Format NIT input
            $('#nitInput').on('input', function() {
                let value = $(this).val().replace(/[^0-9]/g, '');
                if (value.length > 9) {
                    value = value.substring(0, 9) + '-' + value.substring(9, 10);
                }
                $(this).val(value);
                
                // Clear previous timeout
                clearTimeout(nitTimeout);
                
                // Hide company info if input is too short
                if (value.length < 8) {
                    $('#companyInfo').removeClass('show');
                    return;
                }
                
                // Set timeout for NIT verification
                nitTimeout = setTimeout(function() {
                    verifyNIT(value);
                }, 500);
            });
            
            function verifyNIT(nit) {
                // Show loading state
                $('#submitBtn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Verificando...');
                
                // Make AJAX request to verify NIT
                $.ajax({
                    url: '{{ route("login.nit.check") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        nit: nit
                    },                    success: function(response) {
                        if (response.exists) {
                            $('#companyName').text(response.company.name || 'Empresa Verificada');
                            $('#companyDetails').text('NIT: ' + response.company.nit + ' | ' + (response.company.city || 'Ciudad no disponible'));
                            $('#companyInfo').addClass('show');
                            $('#submitBtn').html('<i class="fas fa-check mr-2"></i>Continuar al Paso 2');
                        } else {
                            $('#companyInfo').removeClass('show');
                            $('#submitBtn').html('<i class="fas fa-exclamation-triangle mr-2"></i>NIT no encontrado');
                        }
                    },
                    error: function() {
                        $('#companyInfo').removeClass('show');
                        $('#submitBtn').html('<i class="fas fa-arrow-right mr-2"></i>Continuar al Paso 2');
                    }
                });
            }
            
            // Form validation
            $('#nitForm').on('submit', function(e) {
                const nit = $('#nitInput').val();
                if (nit.length < 8) {
                    e.preventDefault();
                    alert('Por favor ingresa un NIT válido de al menos 8 dígitos.');
                }
            });
        });
    </script>
</body>
</html>
