@extends('layouts.app') {{-- Garante que o layout base (CSS, JS) seja incluído --}}

@section('content')
<div class="welcome-container text-center text-white d-flex flex-column justify-content-center align-items-center">
    <div class="welcome-content animate__animated animate__fadeInUp">
        <h1 class="display-3 fw-bolder mb-4">
            <i class="bi bi-book-half me-3"></i> Sua Biblioteca, Seu Mundo
        </h1>
        <p class="lead mb-5 fs-5">
            Organize, descubra e conecte-se com o universo da leitura. Tudo em um só lugar.
        </p>
        <div class="d-grid gap-3 col-md-6 mx-auto">
            <a href="{{ route('books.index') }}" class="btn btn-light btn-lg rounded-pill shadow-lg animate__animated animate__bounceIn">
                <i class="bi bi-arrow-right-circle-fill me-2"></i> Explorar Coleção
            </a>
        </div>
    </div>
</div>

{{-- Incluir Bootstrap Icons (se ainda não estiver no seu layout base) --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
{{-- Incluir Animate.css (para as animações) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
{{-- Incluir Google Fonts para a fonte Poppins --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">


<style>
    /* Estilos para o container principal */
    .welcome-container {
        width: 100%;
        height: 100vh;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://source.unsplash.com/random/1920x1080/?library,books,study') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Poppins', sans-serif; /* Uma fonte moderna */
    }

    /* Ajustes no conteúdo */
    .welcome-content {
        max-width: 100%;
        padding: 2rem;
        background-color: rgba(0, 0, 0, 0.5); /* Fundo semi-transparente para o texto */
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
    }

    /* Estilos do botão */
    .btn-light {
        color: #007bff; /* Texto azul no botão branco */
        font-weight: bold;
        transition: all 0.3s ease;
    }
    .btn-light:hover {
        background-color: #e2e6ea; /* Levemente mais escuro ao passar o mouse */
        transform: translateY(-3px); /* Efeito de elevação */
        box-shadow: 0 6px 15px rgba(0, 123, 255, 0.3);
    }

    /* Ajustes para a animação do botão (BounceIn) */
    .animate__bounceIn {
        animation-duration: 1.5s; /* Duração da animação */
        animation-delay: 0.5s; /* Um pequeno atraso para a animação começar */
    }
</style>
@endsection