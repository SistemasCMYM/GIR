{{-- resources/views/landing.blade.php --}}
@extends('layouts.dashboard')

@section('content')
    <div class="landing-bg d-flex align-items-center min-vh-100">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-md-6 mb-5 mb-md-0">
                    <h1 class="display-3 fw-bold mb-4 text-primary">Bienvenido a <span class="text-dark">GIR-365</span></h1>
                    <p class="lead mb-4">GIR-365 es una plataforma integral para la gestión de Hallazgos y el módulo
                        Psicosocial en tu empresa. Optimiza procesos, centraliza información y mejora la toma de decisiones.
                    </p>
                    <ul class="list-unstyled mb-4">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> <strong>Hallazgos:</strong>
                            Registro, seguimiento y cierre de hallazgos.</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i>
                            <strong>Psicosocial:</strong> Gestión y análisis de riesgos psicosociales.
                        </li>
                    </ul>
                    <a href="{{ route('login.nit') }}" class="btn btn-lg btn-primary px-5 py-3 shadow">Iniciar sesión</a>
                </div>
                <div class="col-md-6 text-center">
                    <img src="{{ asset('img/AdminLTEFullLogo.png') }}" alt="GIR-365 Logo" class="img-fluid landing-logo">
                </div>
            </div>
        </div>
    </div>
    <style>
        .landing-bg {
            background: linear-gradient(120deg, #e0e7ff 0%, #f8fafc 100%);
        }

        .landing-logo {
            max-width: 400px;
            filter: drop-shadow(0 0 32px #b6b6b6);
        }
    </style>
@endsection
