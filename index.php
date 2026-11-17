<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
  <title>Sistema de Padrón Electoral y Estadísticas Ejecutivas</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Google Fonts: Plus Jakarta Sans -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <!-- SheetJS (Exportación nativa a Excel .xlsx) -->
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <!-- html2pdf.js (Exportación a PDF) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

  <style>
    body {
      font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
      transition: background-color 0.35s ease, color 0.35s ease;
    }

    /* 1. Botones modernos, grandes y dinámicos */
    .btn-modern {
      padding: 0.75rem 1.35rem;
      font-size: 0.925rem;
      font-weight: 700;
      border-radius: 0.875rem;
      transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 12px -2px rgba(0, 0, 0, 0.12), 0 2px 6px -1px rgba(0, 0, 0, 0.07);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.6rem;
      cursor: pointer;
      user-select: none;
    }
    .btn-modern:hover:not(:disabled) {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px -4px rgba(0, 0, 0, 0.18), 0 4px 8px -2px rgba(0, 0, 0, 0.1);
      filter: brightness(1.05);
    }
    .btn-modern:active:not(:disabled) {
      transform: scale(0.95) translateY(0);
      box-shadow: 0 2px 6px -1px rgba(0, 0, 0, 0.12);
    }
    .btn-modern:disabled {
      opacity: 0.55;
      cursor: not-allowed;
    }

    /* Botones de acción en tablas (Más grandes y visibles) */
    .btn-action {
      padding: 0.55rem 0.95rem;
      font-size: 0.82rem;
      font-weight: 700;
      border-radius: 0.75rem;
      transition: all 0.2s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
    }
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.14);
    }
    .btn-action:active {
      transform: scale(0.93);
    }

    /* Soporte para scroll táctil y ocultar barra de desplazamiento en móvil */
    .no-scrollbar::-webkit-scrollbar {
      display: none;
    }
    .no-scrollbar {
      -ms-overflow-style: none;
      scrollbar-width: none;
    }
    .touch-scroll {
      -webkit-overflow-scrolling: touch;
    }

    /* 2. Temas de fondo personalizables */
    body.theme-azul {
      background-color: #dbeafe;
      background-image: radial-gradient(at 0% 0%, #bfdbfe 0px, transparent 50%), radial-gradient(at 100% 100%, #e0e7ff 0px, transparent 50%);
      color: #1e293b;
    }
    body.theme-verde {
      background-color: #d1fae5;
      background-image: radial-gradient(at 0% 0%, #a7f3d0 0px, transparent 50%), radial-gradient(at 100% 100%, #dcfce7 0px, transparent 50%);
      color: #1e293b;
    }
    body.theme-gris {
      background-color: #e2e8f0;
      background-image: radial-gradient(at 0% 0%, #cbd5e1 0px, transparent 50%);
      color: #1e293b;
    }
    body.theme-blanco {
      background-color: #ffffff;
      color: #1e293b;
    }
    body.theme-morado {
      background-color: #f3e8ff;
      background-image: radial-gradient(at 0% 0%, #e9d5ff 0px, transparent 50%), radial-gradient(at 100% 100%, #fae8ff 0px, transparent 50%);
      color: #1e293b;
    }
    body.theme-negro {
      background-color: #0f172a;
      background-image: radial-gradient(at 0% 0%, #1e293b 0px, transparent 50%), radial-gradient(at 100% 100%, #090d16 0px, transparent 50%);
      color: #f1f5f9;
    }
    body.theme-negro .surface-card {
      background-color: #1e293b !important;
      border-color: #334155 !important;
      color: #f1f5f9 !important;
    }
    body.theme-negro .surface-sub {
      background-color: #0f172a !important;
      border-color: #334155 !important;
      color: #cbd5e1 !important;
    }
    body.theme-negro .text-slate-800,
    body.theme-negro .text-slate-900,
    body.theme-negro .text-slate-700 {
      color: #f1f5f9 !important;
    }
    body.theme-negro .text-slate-600,
    body.theme-negro .text-slate-500 {
      color: #94a3b8 !important;
    }
    body.theme-negro input,
    body.theme-negro select {
      background-color: #0f172a !important;
      border-color: #475569 !important;
      color: #f8fafc !important;
    }
    body.theme-negro tbody tr:hover {
      background-color: #334155 !important;
    }

    @media print {
      body * { visibility: hidden !important; }
      #printArea, #printArea * { visibility: visible !important; }
      #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        display: block !important;
        background: white !important;
        color: black !important;
      }
      .no-print { display: none !important; }
    }
  </style>
</head>
<body class="theme-gris min-h-screen flex flex-col">

  <!-- ===================================================================== -->
  <!-- PANTALLA DE INICIO DE SESIÓN OBLIGATORIO                              -->
  <!-- ===================================================================== -->
  <div id="loginScreen" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-6 md:p-8 bg-gradient-to-br from-indigo-950 via-slate-900 to-purple-950 overflow-y-auto">
    <div class="max-w-4xl w-full grid grid-cols-1 md:grid-cols-12 bg-white rounded-3xl shadow-2xl overflow-hidden border border-white/20 my-auto">
      
      <!-- Panel Izquierdo: Branding Oficial María Castillo (Sin credenciales demo) -->
      <div class="md:col-span-5 bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 text-white p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden">
        <div class="relative z-10 space-y-4">
          <!-- Logo / Imagen de Campaña -->
          <div class="rounded-2xl overflow-hidden border-2 border-amber-400 shadow-xl bg-white max-w-[240px] md:max-w-full mx-auto">
            <img src="formulario-maria-castillo.jpg" alt="Yo Estoy con la Profesora María Castillo 2028/2032 PRM" class="w-full h-auto object-contain">
          </div>
          
          <div class="text-center md:text-left pt-1">
            <h2 class="text-lg sm:text-xl font-black text-white leading-tight">
              PADRÓN ELECTORAL Y GESTIÓN POLÍTICA
            </h2>
            <p class="text-xs text-amber-300 font-semibold mt-1">«Aprender También es Crecer»</p>
          </div>

          <div class="hidden sm:block p-3.5 rounded-2xl bg-white/10 border border-amber-400/30 text-indigo-100 text-xs leading-relaxed italic">
            <i class="fa-solid fa-quote-left text-amber-400 mr-1"></i>
            Cada persona que sumamos con respeto, educación y compromiso fortalece nuestra democracia y construye un futuro de progreso para todos.
            <i class="fa-solid fa-quote-right text-amber-400 ml-1"></i>
          </div>
        </div>

        <div class="relative z-10 pt-4 mt-4 border-t border-indigo-800/60 flex items-center justify-between text-xs text-indigo-300">
          <span class="text-[11px] font-medium"><i class="fa-solid fa-shield-halved text-emerald-400 mr-1"></i> Sistema Seguro</span>
          <span id="dbLoginStatus" class="text-[10px] font-bold text-emerald-300 bg-emerald-950/70 px-2.5 py-1 rounded-full border border-emerald-500/30">
            <i class="fa-solid fa-database text-emerald-400 mr-1"></i> MySQL Hostinger
          </span>
        </div>
      </div>

      <!-- Panel Derecho: Formulario de Autenticación para Todos los Usuarios -->
      <div class="md:col-span-7 p-6 sm:p-8 md:p-10 flex flex-col justify-center bg-white text-slate-800">
        <div class="mb-6">
          <div class="flex items-center gap-2 mb-1.5">
            <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
            <span class="text-xs font-black uppercase tracking-widest text-indigo-600">Acceso Autorizado</span>
          </div>
          <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h3>
          <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Ingrese sus credenciales de usuario o cédula asignada para acceder al sistema.
          </p>
        </div>

        <div id="loginErrorBox" class="hidden mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-start gap-3">
          <i class="fa-solid fa-circle-exclamation text-lg mt-0.5 text-rose-600 shrink-0"></i>
          <div class="flex-1 min-w-0">
            <p id="loginErrorTitle" class="font-bold">Error de autenticación</p>
            <p id="loginErrorMsg" class="text-xs mt-0.5"></p>
          </div>
        </div>

        <form onsubmit="procesarLogin(event)" class="space-y-4 sm:space-y-5">
          <div>
            <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
              <i class="fa-solid fa-user text-indigo-600 mr-1.5"></i> Nombre de Usuario o Cédula
            </label>
            <input type="text" id="loginUsername" required autocomplete="username"
              placeholder="Ej: Directora, Administrador o Coordinador"
              class="w-full px-4 py-3 sm:py-3.5 rounded-xl border border-slate-300 text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600 text-base sm:text-sm font-medium transition shadow-xs">
          </div>

          <div>
            <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-1.5">
              <i class="fa-solid fa-lock text-indigo-600 mr-1.5"></i> Contraseña de Acceso
            </label>
            <div class="relative">
              <input type="password" id="loginPassword" required autocomplete="current-password"
                placeholder="Ingrese su contraseña"
                class="w-full px-4 py-3 sm:py-3.5 pr-12 rounded-xl border border-slate-300 text-slate-900 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-600 text-base sm:text-sm font-medium transition shadow-xs">
              <button type="button" onclick="toggleVerPassword('loginPassword', 'iconEyeLogin')"
                class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 p-2 text-base"
                title="Mostrar u ocultar contraseña">
                <i id="iconEyeLogin" class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between text-[11px] sm:text-xs text-slate-500 pt-1">
            <span class="flex items-center gap-1">
              <i class="fa-solid fa-shield-halved text-emerald-600"></i> Límite de seguridad: <strong>3 intentos</strong>
            </span>
            <button type="button" onclick="desbloquearEmergencia()" class="text-indigo-600 hover:underline font-semibold">
              Restablecer claves
            </button>
          </div>

          <button type="submit" id="btnSubmitLogin"
            class="btn-modern w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm sm:text-base py-3.5 sm:py-4 shadow-lg shadow-indigo-600/30">
            <i class="fa-solid fa-right-to-bracket mr-1.5"></i> Ingresar al Sistema Electoral
          </button>
        </form>

        <p class="text-center text-[11px] text-slate-400 mt-6">
          Plataforma Institucional Oficial · Campaña 2028 - 2032
        </p>
      </div>
    </div>
  </div>

    <!-- ===================================================================== -->
  <!-- APLICACIÓN PRINCIPAL                                                  -->
  <!-- ===================================================================== -->
  <div id="appContainer" class="hidden flex-1 flex flex-col">

    <!-- CABECERA EJECUTIVA RESPONSIVA -->
    <header class="bg-indigo-950 text-white shadow-xl sticky top-0 z-40 no-print border-b border-indigo-800">
      <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2.5 sm:py-3.5 flex flex-wrap justify-between items-center gap-3">
        
        <div class="flex items-center space-x-2.5 sm:space-x-3.5 min-w-0">
          <img src="formulario-maria-castillo.jpg" alt="Yo Estoy con la Profesora María Castillo 2028/2032 PRM" class="h-10 sm:h-12 w-20 sm:w-28 rounded-xl object-cover object-left border-2 border-amber-400 shadow-md shrink-0 bg-white">
          <div class="min-w-0">
            <h1 class="text-xs sm:text-base md:text-lg font-extrabold tracking-tight leading-tight truncate sm:whitespace-normal">PADRÓN ELECTORAL · MARÍA CASTILLO</h1>
            <p class="text-[10px] sm:text-xs text-amber-300 font-semibold truncate sm:whitespace-normal">«Aprender También es Crecer» · 2028 - 2032</p>
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
          <!-- Indicador de Base de Datos MySQL Hostinger -->
          <div id="dbStatusBadge" class="hidden sm:inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-xl text-[11px] sm:text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span id="dbStatusText">MySQL Hostinger</span>
          </div>

          <!-- Selector de Tema / Color de Fondo -->
          <div class="relative">
            <button onclick="toggleMenuColores()" class="btn-modern bg-indigo-900 hover:bg-indigo-800 text-white border border-indigo-700 !py-2 sm:!py-2.5 !px-2.5 sm:!px-4 !text-xs">
              <i class="fa-solid fa-palette text-amber-400 text-sm"></i>
              <span class="hidden sm:inline">Color de Fondo</span>
              <i class="fa-solid fa-chevron-down text-[10px]"></i>
            </button>
            <div id="menuColores" class="hidden absolute right-0 mt-2 w-60 bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 p-3 z-50">
              <p class="text-xs font-extrabold uppercase text-slate-500 mb-2 px-1">Personalizar Fondo</p>
              <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                <button onclick="cambiarColorFondo('azul')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-blue-500 border border-blue-700 shadow-inner"></span> Azul
                </button>
                <button onclick="cambiarColorFondo('verde')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-emerald-500 border border-emerald-700 shadow-inner"></span> Verde
                </button>
                <button onclick="cambiarColorFondo('gris')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-slate-400 border border-slate-600 shadow-inner"></span> Gris
                </button>
                <button onclick="cambiarColorFondo('blanco')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-white border-2 border-slate-400 shadow-inner"></span> Blanco
                </button>
                <button onclick="cambiarColorFondo('negro')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-slate-900 border border-black shadow-inner"></span> Negro
                </button>
                <button onclick="cambiarColorFondo('morado')" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 border border-slate-200 transition">
                  <span class="w-5 h-5 rounded-full bg-purple-600 border border-purple-800 shadow-inner"></span> Morado
                </button>
              </div>
            </div>
          </div>

          <!-- Usuario Autenticado -->
          <div class="flex items-center gap-2 sm:gap-3 bg-indigo-900/90 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-2xl border border-indigo-700">
            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-amber-400 text-indigo-950 flex items-center justify-center font-black text-xs sm:text-sm shrink-0">
              <i id="headerUserIcon" class="fa-solid fa-user-shield"></i>
            </div>
            <div class="text-left min-w-0">
              <div class="flex items-center gap-1.5 sm:gap-2">
                <span id="headerUserName" class="text-xs sm:text-sm font-bold text-white leading-none truncate max-w-[100px] sm:max-w-none">Usuario</span>
                <span id="headerUserRoleBadge" class="text-[9px] sm:text-[10px] font-black uppercase px-1.5 sm:px-2 py-0.5 rounded bg-amber-400 text-indigo-950 shrink-0">ROL</span>
              </div>
              <p id="headerUserMeta" class="text-[10px] sm:text-[11px] text-indigo-300 mt-0.5 hidden sm:block">Cédula: ---</p>
            </div>
          </div>

          <button onclick="abrirModalCambiarPass(false)" class="btn-modern bg-indigo-800 hover:bg-indigo-700 text-amber-300 border border-indigo-600 !py-2 sm:!py-2.5 !px-2.5 sm:!px-3.5 !text-xs" title="Cambiar mi contraseña">
            <i class="fa-solid fa-key"></i>
            <span class="hidden md:inline">Clave</span>
          </button>

          <button onclick="cerrarSesion()" class="btn-modern bg-rose-600 hover:bg-rose-700 text-white !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs">
            <i class="fa-solid fa-power-off"></i>
            <span class="hidden sm:inline">Salir</span>
          </button>
        </div>
      </div>

      <!-- MENÚ DE NAVEGACIÓN PRINCIPAL CON SLIDER HORIZONTAL RESPONSIVO -->
      <div class="bg-indigo-900/80 border-t border-indigo-800/80">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 py-2 flex flex-col xl:flex-row items-stretch xl:items-center justify-between gap-2">
          <nav class="flex items-center gap-1.5 sm:gap-2 overflow-x-auto pb-1 sm:pb-0 w-full xl:w-auto scrollbar-none no-scrollbar touch-scroll">
            <button id="navDashboard" onclick="cambiarVista('dashboard')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-amber-400 text-indigo-950 whitespace-nowrap shrink-0">
              <i class="fa-solid fa-chart-pie text-sm"></i>
              <span>Dashboard</span>
            </button>
            <button id="navPadron" onclick="cambiarVista('padron')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800 whitespace-nowrap shrink-0">
              <i class="fa-solid fa-users-viewfinder text-sm"></i>
              <span>Padrón Electoral</span>
            </button>
            <button id="navComparador" onclick="cambiarVista('comparador')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800 whitespace-nowrap shrink-0">
              <i class="fa-solid fa-scale-balanced text-sm"></i>
              <span>Comparador</span>
            </button>
            <button id="navSeguridad" onclick="cambiarVista('seguridad')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800 whitespace-nowrap shrink-0">
              <i class="fa-solid fa-user-lock text-sm"></i>
              <span>Seguridad & Bitácora</span>
            </button>
            <button id="navAsistencia" onclick="cambiarVista('asistencia')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800 hidden whitespace-nowrap shrink-0">
              <i class="fa-solid fa-clipboard-check text-sm"></i>
              <span>Pase de Lista</span>
            </button>
            <button id="navMisionVision" onclick="cambiarVista('misionVision')" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800 whitespace-nowrap shrink-0">
              <i class="fa-solid fa-star text-sm"></i>
              <span>Misión y Valores</span>
            </button>
          </nav>

          <!-- Botones de Acción Rápida (Inscribir Persona y Agregar Coordinador) -->
          <div class="flex items-center gap-2 w-full xl:w-auto justify-end pt-1 xl:pt-0">
            <button id="btnTopNuevoCoordinador" onclick="abrirModalNuevoCoordinador()" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-amber-400 hover:bg-amber-300 text-indigo-950 flex-1 sm:flex-initial whitespace-nowrap">
              <i class="fa-solid fa-user-tie"></i>
              <span>+ Coordinador</span>
            </button>
            <button id="btnTopNuevoAfiliado" onclick="abrirModalNuevo()" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-emerald-600 hover:bg-emerald-500 text-white flex-1 sm:flex-initial whitespace-nowrap">
              <i class="fa-solid fa-user-plus"></i>
              <span>+ Inscribir Miembro</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- CONTENEDOR PRINCIPAL RESPONSIVO -->
    <main class="max-w-7xl mx-auto px-3 sm:px-4 md:px-6 py-4 sm:py-6 space-y-4 sm:space-y-6 flex-1 w-full no-print">

      <!-- BANNER INSPIRADOR PERSISTENTE EN TODAS LAS VISTAS -->
      <div class="rounded-2xl bg-gradient-to-r from-blue-900 via-indigo-900 to-sky-800 text-white p-4 sm:p-5 shadow-lg border border-amber-400/40 flex flex-col lg:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-4">
          <img src="formulario-maria-castillo.jpg" alt="Yo Estoy con la Profesora María Castillo 2028/2032 PRM" class="w-full sm:w-64 h-auto rounded-xl object-contain border-2 border-amber-400 shadow-lg shrink-0 bg-white">
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black uppercase bg-amber-400 text-indigo-950">
                <i class="fa-solid fa-lightbulb mr-1"></i> Aprender También es Crecer
              </span>
              <span class="text-xs font-bold text-sky-200">María Castillo (La Profe) · 2028/2032 · PRM</span>
            </div>
            <p class="text-sm sm:text-base font-bold text-white mt-1.5 leading-snug">
              «El verdadero liderazgo nace del ejemplo, la educación y el servicio cercano: cada registro en este padrón representa una familia con esperanza y un paso firme hacia un mejor mañana.»
            </p>
          </div>
        </div>
        <button onclick="cambiarVista('misionVision')" class="shrink-0 px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-indigo-950 font-extrabold text-xs shadow-md transition flex items-center gap-2">
          <i class="fa-solid fa-star"></i>
          <span>Ver Misión, Visión y Valores</span>
        </button>
      </div>

      <!-- BANNER DEL ROL AUTENTICADO -->
      <div class="surface-card bg-white border-l-8 border-indigo-600 p-5 rounded-2xl shadow-md flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div>
          <div class="flex items-center gap-2">
            <span id="roleBadge" class="px-3 py-1 rounded-full text-xs font-extrabold uppercase bg-indigo-100 text-indigo-800">Rol Activo</span>
            <span id="lblUltimoAcceso" class="text-xs text-slate-500 font-medium"><i class="fa-regular fa-clock mr-1"></i>Sesión activa</span>
          </div>
          <h2 id="roleUserName" class="text-xl font-extrabold text-slate-900 mt-1">Nombre del Usuario</h2>
          <p id="roleUserDesc" class="text-sm text-slate-600 mt-0.5">Descripción de permisos del rol.</p>
        </div>

        <div id="panelExportacionesEjecutivas" class="flex flex-wrap items-center gap-2 sm:gap-2.5 w-full lg:w-auto">
          <button onclick="exportarExcelCompleto()" class="btn-modern flex-1 sm:flex-initial !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-emerald-600 hover:bg-emerald-700 text-white whitespace-nowrap">
            <i class="fa-solid fa-file-excel text-sm"></i>
            <span>Excel (.xlsx)</span>
          </button>
          <button onclick="exportarReportePDF()" class="btn-modern flex-1 sm:flex-initial !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-rose-600 hover:bg-rose-700 text-white whitespace-nowrap">
            <i class="fa-solid fa-file-pdf text-sm"></i>
            <span>Dashboard PDF</span>
          </button>
          <button onclick="imprimirReporteEstadistico()" class="btn-modern flex-1 sm:flex-initial !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-slate-800 hover:bg-slate-900 text-white whitespace-nowrap">
            <i class="fa-solid fa-print text-sm"></i>
            <span>Imprimir</span>
          </button>
        </div>
      </div>

      <!-- VISTA 1: DASHBOARD DE ESTADÍSTICAS Y GRÁFICOS -->
      <section id="vistaDashboard" class="space-y-6">
        
        <!-- 5 TARJETAS KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
          <div class="surface-card bg-white rounded-2xl p-5 shadow-md border border-slate-200/80 flex items-center justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Afiliados</p>
              <h3 id="kpiTotalAfiliados" class="text-3xl font-black text-indigo-600 mt-1">0</h3>
              <p class="text-[11px] text-slate-500 mt-1">100% del padrón activo</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-solid fa-users"></i>
            </div>
          </div>

          <div class="surface-card bg-white rounded-2xl p-5 shadow-md border border-slate-200/80 flex items-center justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Coordinadores</p>
              <h3 id="kpiTotalCoordinadores" class="text-3xl font-black text-blue-600 mt-1">3</h3>
              <p class="text-[11px] text-slate-500 mt-1">Coordinaciones activas</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-solid fa-sitemap"></i>
            </div>
          </div>

          <div class="surface-card bg-white rounded-2xl p-5 shadow-md border-l-4 border-emerald-500 flex items-center justify-between">
            <div class="pr-2">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800">Mayor Rendimiento</span>
              <h3 id="kpiMayorNombre" class="text-base font-extrabold text-slate-900 mt-1 truncate">---</h3>
              <p id="kpiMayorDetalle" class="text-xs font-bold text-emerald-600 mt-0.5">0 afiliados (0%)</p>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-2xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-solid fa-trophy"></i>
            </div>
          </div>

          <div class="surface-card bg-white rounded-2xl p-5 shadow-md border-l-4 border-rose-500 flex items-center justify-between">
            <div class="pr-2">
              <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-100 text-rose-800">Menor Cantidad</span>
              <h3 id="kpiMenorNombre" class="text-base font-extrabold text-slate-900 mt-1 truncate">---</h3>
              <p id="kpiMenorDetalle" class="text-xs font-bold text-rose-600 mt-0.5">0 afiliados (0%)</p>
            </div>
            <div class="w-12 h-12 shrink-0 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-solid fa-arrow-trend-down"></i>
            </div>
          </div>

          <div class="surface-card bg-white rounded-2xl p-5 shadow-md border border-slate-200/80 flex items-center justify-between">
            <div>
              <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Promedio / Coord.</p>
              <h3 id="kpiPromedioCoord" class="text-3xl font-black text-purple-600 mt-1">0</h3>
              <p class="text-[11px] text-slate-500 mt-1">Afiliados por coordinación</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl shadow-inner">
              <i class="fa-solid fa-chart-line"></i>
            </div>
          </div>
        </div>

        <!-- SEMÁFORO DE RENDIMIENTO POR COORDINADOR + BOTÓN AGREGAR COORDINADOR -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
            <div>
              <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-gauge-high text-indigo-600"></i>
                Semáforo de Rendimiento y Participación por Coordinador
              </h3>
              <p class="text-xs text-slate-500">Cálculo automático de porcentajes sobre el padrón general e indicadores visuales de desempeño.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5 text-xs font-bold">
              <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Verde: Mayor
              </span>
              <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Amarillo: Medio
              </span>
              <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Rojo: Menor
              </span>
              <button id="btnDashNuevoCoordinador" onclick="abrirModalNuevoCoordinador()" class="btn-action bg-indigo-600 hover:bg-indigo-700 text-white ml-1">
                <i class="fa-solid fa-user-tie"></i> + Agregar Coordinador
              </button>
              <button id="btnDashNuevaPersona" onclick="abrirModalNuevo()" class="btn-action bg-emerald-600 hover:bg-emerald-700 text-white">
                <i class="fa-solid fa-user-plus"></i> + Inscribir Miembros (Militante)
              </button>
            </div>
          </div>
          <div id="cardsCoordinadores" class="grid grid-cols-1 md:grid-cols-3 gap-4"></div>
        </div>

        <!-- GRÁFICOS CIRCULAR Y DE BARRAS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
          <div class="lg:col-span-5 surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                  <i class="fa-solid fa-chart-pie text-indigo-600"></i> Distribución Porcentual (%)
                </h4>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700">Gráfico Circular</span>
              </div>
              <p class="text-xs text-slate-500 mb-4">Porcentaje que representa cada coordinador respecto al total de afiliados.</p>
            </div>
            <div class="relative h-64 w-full flex items-center justify-center">
              <canvas id="chartPieCoordinadores"></canvas>
            </div>
            <div id="leyendaPorcentajesPie" class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-2 sm:grid-cols-3 gap-2 text-center text-xs"></div>
          </div>

          <div class="lg:col-span-7 surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md flex flex-col justify-between">
            <div>
              <div class="flex items-center justify-between mb-2">
                <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                  <i class="fa-solid fa-chart-column text-emerald-600"></i> Cantidad de Afiliados por Coordinador
                </h4>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700">Gráfico de Barras + Semáforo</span>
              </div>
              <p class="text-xs text-slate-500 mb-4">Comparación directa de afiliados inscritos coloreada según nivel de rendimiento.</p>
            </div>
            <div class="relative h-72 w-full">
              <canvas id="chartBarCoordinadores"></canvas>
            </div>
          </div>
        </div>

        <!-- GRÁFICO DE TENDENCIAS MENSUAL -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
            <div>
              <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-chart-area text-purple-600"></i> Evolución y Crecimiento Mensual del Padrón
              </h4>
              <p class="text-xs text-slate-500">Tendencia histórica y actual de afiliaciones registradas por coordinación territorial.</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-purple-50 text-purple-700">Gráfico de Tendencias</span>
          </div>
          <div class="relative h-64 w-full">
            <canvas id="chartTrendMeses"></canvas>
          </div>
        </div>
      </section>

      <!-- VISTA 2: TABLA DEL PADRÓN ELECTORAL -->
      <section id="vistaPadron" class="space-y-6 hidden">
        <div class="surface-card bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
          <div class="surface-sub p-3 sm:p-4 bg-slate-50 border-b border-slate-200 flex flex-col md:flex-row justify-between items-stretch md:items-center gap-3">
            <div class="relative flex-1 min-w-0">
              <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
              <input type="text" id="inputBuscar" oninput="renderPadron()" placeholder="Buscar afiliado por Nombre, Cédula, Folio, Centro o Mesa..."
                class="w-full pl-9 pr-4 py-2.5 text-base sm:text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium shadow-xs">
            </div>

            <div class="flex flex-wrap items-center gap-2 sm:gap-3">
              <div id="wrapperFiltroCoord" class="flex items-center gap-1.5 sm:gap-2 flex-1 sm:flex-initial">
                <span class="text-xs text-slate-600 font-extrabold uppercase shrink-0"><i class="fa-solid fa-filter mr-1 text-indigo-600"></i>Coord:</span>
                <select id="filtroCoordinador" onchange="renderPadron()" class="text-xs sm:text-sm px-2.5 sm:px-3 py-2 sm:py-2.5 bg-white border border-slate-300 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full sm:w-auto">
                  <option value="todos">Todos los Coordinadores</option>
                </select>
              </div>
              <button id="btnPadronNuevoCoord" onclick="abrirModalNuevoCoordinador()" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-amber-500 hover:bg-amber-600 text-indigo-950 flex-1 sm:flex-initial whitespace-nowrap">
                <i class="fa-solid fa-user-tie"></i> <span class="hidden sm:inline">Agregar</span> Coordinador
              </button>
              <button id="btnPadronVerMiFormulario" onclick="verFormularioCoordinador(rolActual)" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-amber-400 hover:bg-amber-300 text-indigo-950 hidden flex-1 sm:flex-initial whitespace-nowrap">
                <i class="fa-solid fa-clipboard-list"></i> Mi Formulario (10)
              </button>
              <button id="btnPadronNuevo" onclick="abrirModalNuevo()" class="btn-modern !py-2 sm:!py-2.5 !px-3 sm:!px-4 !text-xs bg-indigo-600 hover:bg-indigo-700 text-white flex-1 sm:flex-initial whitespace-nowrap">
                <i class="fa-solid fa-user-plus"></i> Inscribir Miembro
              </button>
            </div>
          </div>

          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-4">Folio / Cédula</th>
                  <th class="p-4">Nombre Completo</th>
                  <th class="p-4">Teléfono</th>
                  <th class="p-4">Centro de Votación / Dirección</th>
                  <th class="p-4">Mesa Electoral</th>
                  <th class="p-4">Fecha Registro</th>
                  <th class="p-4">Coordinador Asignado</th>
                  <th class="p-4 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaIntegrantes" class="divide-y divide-slate-200/70 text-slate-700"></tbody>
            </table>
          </div>

          <div class="surface-sub p-4 bg-slate-50 border-t border-slate-200 text-xs text-slate-600 font-semibold flex flex-wrap justify-between items-center gap-2">
            <span id="lblConteo">Cargando registros...</span>
            <span><i class="fa-solid fa-circle-check text-emerald-600 mr-1"></i> Los cambios actualizan el Dashboard en tiempo real</span>
          </div>
        </div>
      </section>

      <!-- VISTA 3: COMPARADOR DE COORDINADORES -->
      <section id="vistaComparador" class="space-y-6 hidden">
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap justify-between items-start gap-4 mb-6">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Análisis Comparativo Directo</span>
              <h3 class="text-xl font-extrabold text-slate-900 mt-1">Comparador de Rendimiento entre Coordinadores</h3>
              <p class="text-sm text-slate-500">Seleccione dos o más coordinadores para contrastar su volumen de afiliados, brecha porcentual y liderazgo.</p>
            </div>
            <div id="contenedorChecksComparador" class="flex flex-wrap gap-3 bg-slate-100 p-3 rounded-2xl border border-slate-200 surface-sub"></div>
          </div>

          <div id="bannerLiderComparacion" class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg flex flex-wrap justify-between items-center gap-4">
            <div class="flex items-center gap-4">
              <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl">
                <i class="fa-solid fa-crown text-amber-300"></i>
              </div>
              <div>
                <span class="text-xs uppercase font-extrabold tracking-wider text-emerald-100">Líder en la Comparación Seleccionada</span>
                <h4 id="compLiderNombre" class="text-2xl font-black">---</h4>
                <p id="compLiderDesc" class="text-xs text-emerald-100 mt-0.5">Posee la mayor cantidad de afiliados dentro del grupo comparado.</p>
              </div>
            </div>
            <div class="text-right">
              <span id="compLiderPorcentaje" class="text-3xl font-black text-amber-300">0%</span>
              <p class="text-xs text-emerald-100">de participación del total general</p>
            </div>
          </div>

          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-4">Posición</th>
                  <th class="p-4">Coordinador y Zona</th>
                  <th class="p-4">Afiliados Registrados</th>
                  <th class="p-4">% del Padrón General</th>
                  <th class="p-4">% dentro del Grupo Comparado</th>
                  <th class="p-4">Indicador de Rendimiento</th>
                </tr>
              </thead>
              <tbody id="tablaComparativaBody" class="divide-y divide-slate-200"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VISTA 4: SEGURIDAD, COORDINADORES, USUARIOS Y BITÁCORA -->
      <section id="vistaSeguridad" class="space-y-6 hidden">

        <!-- Gestión de Coordinadores (Accesible para Directora María y Administrador) -->
        <div id="panelGestionCoordinadores" class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Dirección Ejecutiva y Administración</span>
              <h3 class="text-lg font-extrabold text-slate-900">Directorio de Coordinadores Territoriales</h3>
              <p class="text-xs text-slate-500">La Directora María Castillo y el Administrador pueden agregar, modificar o dar de baja coordinadores.</p>
            </div>
            <button onclick="abrirModalNuevoCoordinador()" class="btn-modern bg-indigo-600 hover:bg-indigo-700 text-white">
              <i class="fa-solid fa-user-tie"></i> Agregar Nuevo Coordinador
            </button>
          </div>
          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-3.5">Nombre del Coordinador</th>
                  <th class="p-3.5">Zona Territorial</th>
                  <th class="p-3.5">Cédula</th>
                  <th class="p-3.5">Afiliados Inscritos</th>
                  <th class="p-3.5 text-right">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaCoordinadoresGestion" class="divide-y divide-slate-200"></tbody>
            </table>
          </div>
        </div>

        <!-- Panel de Administración de Usuarios y Restablecimiento de Contraseñas -->
        <div id="panelAdminUsuarios" class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Control de Credenciales</span>
              <h3 class="text-lg font-extrabold text-slate-900">Control de Usuarios, Desbloqueo y Restablecimiento de Contraseñas</h3>
              <p class="text-xs text-slate-500">Permite restablecer la contraseña de cualquier usuario a los últimos 4 dígitos de su cédula o desbloquear cuentas.</p>
            </div>
          </div>

          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-3.5">Usuario</th>
                  <th class="p-3.5">Nombre Completo / Rol</th>
                  <th class="p-3.5">Cédula</th>
                  <th class="p-3.5">Clave Inicial (4 dígitos)</th>
                  <th class="p-3.5">Estado / Intentos</th>
                  <th class="p-3.5 text-right">Acciones de Seguridad</th>
                </tr>
              </thead>
              <tbody id="tablaUsuariosSeguridad" class="divide-y divide-slate-200"></tbody>
            </table>
          </div>
        </div>

        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md">
          <div class="flex flex-wrap justify-between items-center gap-4 mb-4">
            <div>
              <h3 class="text-lg font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                Bitácora de Auditoría de Inicios de Sesión
              </h3>
              <p class="text-xs text-slate-500">Registro cronológico de fecha, hora, usuario, rol y resultado de cada acceso al sistema.</p>
            </div>
            <button id="btnLimpiarBitacora" onclick="limpiarBitacora()" class="btn-action bg-slate-200 hover:bg-slate-300 text-slate-700">
              <i class="fa-solid fa-broom"></i> Limpiar Historial
            </button>
          </div>

          <div class="overflow-x-auto max-h-96">
            <table class="w-full text-left text-xs border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold border-b border-slate-200 sticky top-0">
                  <th class="p-3">Fecha y Hora Exacta</th>
                  <th class="p-3">Usuario</th>
                  <th class="p-3">Rol</th>
                  <th class="p-3">Evento / Estado</th>
                </tr>
              </thead>
              <tbody id="tablaBitacoraAccesos" class="divide-y divide-slate-200"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VISTA 5: PASE DE LISTA POR REUNIONES (SOLO ADMINISTRADORES) -->
      <section id="vistaAsistencia" class="space-y-6 hidden">
        <!-- Panel Crear / Seleccionar Reunión -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md space-y-5">
          <div class="flex flex-wrap justify-between items-center gap-3 border-b border-slate-200 pb-4">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Control Exclusivo de Administración</span>
              <h3 class="text-xl font-extrabold text-slate-900 flex items-center gap-2 mt-0.5">
                <i class="fa-solid fa-clipboard-check text-emerald-600"></i>
                Pase de Lista y Asistencia por Reuniones
              </h3>
              <p class="text-xs text-slate-500">Cree reuniones, registre la asistencia de coordinadores y militantes, y exporte o imprima el acta de asistencia.</p>
            </div>
            <div class="flex flex-wrap gap-2">
              <button onclick="exportarAsistenciaExcel()" class="btn-action bg-emerald-600 hover:bg-emerald-700 text-white">
                <i class="fa-solid fa-file-excel"></i> Exportar Asistencia
              </button>
              <button onclick="imprimirPaseDeLista()" class="btn-action bg-slate-800 hover:bg-slate-900 text-white">
                <i class="fa-solid fa-print"></i> Imprimir Acta
              </button>
            </div>
          </div>

          <!-- Formulario Nueva Reunión -->
          <form onsubmit="crearReunion(event)" class="surface-sub bg-slate-50 p-4 rounded-2xl border border-slate-200 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-4">
              <label class="block text-xs font-bold text-slate-700 mb-1">Nombre / Motivo de la Reunión *</label>
              <input type="text" id="reunionTitulo" required placeholder="Ej: Reunión General de Coordinación" class="w-full p-2.5 text-sm border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Fecha *</label>
              <input type="date" id="reunionFecha" required class="w-full p-2.5 text-sm border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Hora</label>
              <input type="time" id="reunionHora" class="w-full p-2.5 text-sm border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-slate-700 mb-1">Lugar</label>
              <input type="text" id="reunionLugar" placeholder="Ej: Local Central" class="w-full p-2.5 text-sm border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            </div>
            <div class="md:col-span-2">
              <button type="submit" class="btn-modern w-full bg-indigo-600 hover:bg-indigo-700 text-white !py-2.5 !text-xs">
                <i class="fa-solid fa-plus"></i> Crear Reunión
              </button>
            </div>
          </form>

          <!-- Selector de Reunión Activa + KPIs -->
          <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[260px]">
              <label class="text-xs font-extrabold uppercase text-slate-600"><i class="fa-solid fa-calendar-days text-indigo-600 mr-1"></i>Reunión Activa:</label>
              <select id="selectReunionAsistencia" onchange="renderPaseDeLista()" class="flex-1 min-w-[220px] p-2.5 text-sm font-bold border border-slate-300 rounded-xl bg-white focus:ring-2 focus:ring-indigo-600 focus:outline-none"></select>
              <button type="button" onclick="eliminarReunionActual()" class="btn-action bg-rose-100 hover:bg-rose-200 text-rose-700" title="Eliminar reunión seleccionada">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
            <div class="flex flex-wrap gap-2 text-xs font-extrabold">
              <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-800 border border-slate-200">Total: <span id="kpiAsisTotal">0</span></span>
              <span class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-200">Presentes: <span id="kpiAsisPresentes">0</span></span>
              <span class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 border border-amber-200">Tarde/Excusa: <span id="kpiAsisTarde">0</span></span>
              <span class="px-3 py-1.5 rounded-xl bg-rose-100 text-rose-800 border border-rose-200">Ausentes: <span id="kpiAsisAusentes">0</span></span>
              <span class="px-3 py-1.5 rounded-xl bg-indigo-100 text-indigo-800 border border-indigo-200">Asistencia: <span id="kpiAsisPct">0%</span></span>
            </div>
          </div>
        </div>

        <!-- Tabla de Pase de Lista -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 shadow-md overflow-hidden">
          <div class="surface-sub p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap justify-between items-center gap-3">
            <div class="flex flex-wrap items-center gap-2 flex-1 min-w-[260px]">
              <div class="relative flex-1 min-w-[200px]">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                <input type="text" id="buscarAsistencia" oninput="renderPaseDeLista()" placeholder="Buscar por Nombre o Cédula..." class="w-full pl-9 pr-3 py-2 text-sm bg-white border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500">
              </div>
              <select id="filtroTipoAsistencia" onchange="renderPaseDeLista()" class="text-sm px-3 py-2 bg-white border border-slate-300 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="todos">Todos (Coordinadores y Militantes)</option>
                <option value="coordinadores">Solo Coordinadores</option>
                <option value="militantes">Solo Militantes</option>
              </select>
            </div>
            <div class="flex flex-wrap gap-2">
              <button type="button" onclick="marcarTodosAsistencia('presente')" class="btn-action bg-emerald-100 hover:bg-emerald-200 text-emerald-800">
                <i class="fa-solid fa-check-double"></i> Todos Presentes
              </button>
              <button type="button" onclick="marcarTodosAsistencia('ausente')" class="btn-action bg-rose-100 hover:bg-rose-200 text-rose-800">
                <i class="fa-solid fa-xmark"></i> Todos Ausentes
              </button>
            </div>
          </div>

          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-3.5 w-12 text-center">#</th>
                  <th class="p-3.5">Nombre Completo</th>
                  <th class="p-3.5">Cédula</th>
                  <th class="p-3.5">Rol / Coordinación</th>
                  <th class="p-3.5 text-center">Estado de Asistencia</th>
                  <th class="p-3.5">Observación / Nota</th>
                </tr>
              </thead>
              <tbody id="tablaPaseDeLista" class="divide-y divide-slate-200/70 text-slate-700"></tbody>
            </table>
          </div>
        </div>

        <!-- CONFRONTE GRÁFICO Y RANKING DE ASISTENCIA RECURRENTE -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 shadow-md space-y-6">
          <div class="flex flex-wrap justify-between items-center gap-3 border-b border-slate-200 pb-4">
            <div>
              <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Análisis Comparativo de Reuniones</span>
              <h4 class="text-lg font-extrabold text-slate-900 flex items-center gap-2 mt-0.5">
                <i class="fa-solid fa-chart-bar text-indigo-600"></i>
                Confronte Gráfico de Asistencia Recurrente
              </h4>
              <p class="text-xs text-slate-500">Comparación acumulada entre todas las reuniones registradas para identificar asistencia constante, tardanzas y ausencias.</p>
            </div>
            <div class="flex items-center gap-2">
              <label class="text-xs font-extrabold uppercase text-slate-600">Comparar:</label>
              <select id="filtroGraficaAsistencia" onchange="actualizarGraficosAsistencia()" class="text-sm px-3 py-2 bg-white border border-slate-300 rounded-xl font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="todos">Todos (Coordinadores y Militantes)</option>
                <option value="coordinadores">Solo Coordinadores</option>
                <option value="militantes">Solo Militantes</option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7 surface-sub bg-slate-50 rounded-2xl border border-slate-200 p-4">
              <h5 class="text-sm font-extrabold text-slate-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-users-line text-emerald-600"></i> Confronte de Asistencia por Persona (Acumulado)
              </h5>
              <p class="text-[11px] text-slate-500 mb-3">Ordenado de mayor a menor cantidad de asistencias presenciales.</p>
              <div class="relative h-80 w-full">
                <canvas id="chartAsistenciaPersonas"></canvas>
              </div>
            </div>

            <div class="lg:col-span-5 surface-sub bg-slate-50 rounded-2xl border border-slate-200 p-4">
              <h5 class="text-sm font-extrabold text-slate-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-indigo-600"></i> Evolución de Asistencia por Reunión (%)
              </h5>
              <p class="text-[11px] text-slate-500 mb-3">Porcentaje de asistencia alcanzado en cada reunión cronológicamente.</p>
              <div class="relative h-80 w-full">
                <canvas id="chartAsistenciaReuniones"></canvas>
              </div>
            </div>
          </div>

          <div class="overflow-x-auto rounded-2xl border border-slate-200">
            <table class="w-full text-left text-xs sm:text-sm border-collapse">
              <thead>
                <tr class="surface-sub bg-slate-100 text-slate-700 uppercase font-extrabold text-xs border-b border-slate-200">
                  <th class="p-3.5 text-center w-12">#</th>
                  <th class="p-3.5">Nombre Completo</th>
                  <th class="p-3.5">Rol / Coordinación</th>
                  <th class="p-3.5 text-center">Reuniones</th>
                  <th class="p-3.5 text-center text-emerald-700">Presentes</th>
                  <th class="p-3.5 text-center text-amber-700">Tarde / Excusa</th>
                  <th class="p-3.5 text-center text-rose-700">Ausentes</th>
                  <th class="p-3.5">% Asistencia</th>
                  <th class="p-3.5">Recurrencia</th>
                </tr>
              </thead>
              <tbody id="tablaConfronteAsistencia" class="divide-y divide-slate-200/70 text-slate-700"></tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- VISTA 6: MISIÓN, VISIÓN Y VALORES DEL PADRÓN ELECTORAL -->
      <section id="vistaMisionVision" class="space-y-6 hidden">
        <!-- Hero Institucional con Imagen Oficial -->
        <div class="rounded-3xl bg-gradient-to-br from-indigo-950 via-blue-900 to-sky-900 text-white p-6 sm:p-8 shadow-xl border border-amber-400/30 flex flex-col lg:flex-row items-center gap-6">
          <img src="formulario-maria-castillo.jpg" alt="Yo Estoy con la Profesora María Castillo 2028/2032 PRM" class="w-full lg:w-80 h-auto rounded-2xl object-contain border-4 border-amber-400 shadow-2xl shrink-0 bg-white">
          <div class="space-y-3 text-center lg:text-left">
            <span class="inline-block px-3 py-1 rounded-full bg-amber-400 text-indigo-950 text-xs font-black uppercase tracking-wider">
              <i class="fa-solid fa-landmark mr-1"></i> Identidad del Padrón Electoral · María Castillo (La Profe)
            </span>
            <h2 class="text-2xl sm:text-3xl font-black leading-tight">
              Misión, Visión y Valores del Equipo Electoral
            </h2>
            <p class="text-sm sm:text-base text-sky-100 leading-relaxed max-w-3xl">
              Bajo el lema <strong>«Aprender También es Crecer»</strong>, nuestro padrón electoral no es solo una base de datos: es una red viva de compromiso ciudadano, organización territorial y servicio transparente para construir comunidades más preparadas, unidas y participativas.
            </p>
          </div>
        </div>

        <!-- Misión y Visión -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <div class="surface-card bg-white rounded-2xl border-t-4 border-indigo-600 p-6 sm:p-7 shadow-md space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-bullseye"></i>
              </div>
              <div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-indigo-600">Propósito</span>
                <h3 class="text-xl font-black text-slate-900">Nuestra Misión</h3>
              </div>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
              Organizar, validar y acompañar de forma cercana, transparente y tecnológica a cada ciudadano y coordinador territorial en el padrón electoral, garantizando información confiable en tiempo real y fomentando la participación cívica, la formación continua y el respeto mutuo en cada recinto y comunidad.
            </p>
          </div>

          <div class="surface-card bg-white rounded-2xl border-t-4 border-amber-500 p-6 sm:p-7 shadow-md space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-eye"></i>
              </div>
              <div>
                <span class="text-xs font-extrabold uppercase tracking-wider text-amber-600">Horizonte</span>
                <h3 class="text-xl font-black text-slate-900">Nuestra Visión</h3>
              </div>
            </div>
            <p class="text-sm text-slate-600 leading-relaxed">
              Consolidar un padrón electoral modelo, íntegro y en crecimiento sostenido, reconocido por su excelencia organizativa, la fuerza de sus coordinaciones territoriales y el liderazgo humano de <strong>María Castillo (La Profe)</strong>, transformando la participación ciudadana en bienestar, educación y progreso real para todos.
            </p>
          </div>
        </div>

        <!-- Valores Fundamentales -->
        <div class="surface-card bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-md space-y-5">
          <div>
            <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-600">Principios que Nos Guían</span>
            <h3 class="text-xl font-black text-slate-900">Nuestros Valores</h3>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg"><i class="fa-solid fa-shield-halved"></i></div>
              <h4 class="font-extrabold text-slate-900">Transparencia y Veracidad</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Cada afiliado, folio y recinto se registra con exactitud, auditoría permanente y claridad total en las estadísticas.</p>
            </div>

            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg"><i class="fa-solid fa-graduation-cap"></i></div>
              <h4 class="font-extrabold text-slate-900">Educación y Crecimiento</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Fieles a <em>«Aprender también es crecer»</em>, impulsamos la capacitación cívica y el desarrollo personal de nuestro equipo.</p>
            </div>

            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg"><i class="fa-solid fa-people-group"></i></div>
              <h4 class="font-extrabold text-slate-900">Unidad y Trabajo en Equipo</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Coordinadores y afiliados trabajamos hombro a hombro, sumando voluntades en cada zona y demarcación.</p>
            </div>

            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center text-lg"><i class="fa-solid fa-hand-holding-heart"></i></div>
              <h4 class="font-extrabold text-slate-900">Cercanía y Vocación de Servicio</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Tratamos a cada ciudadano con respeto, calidez humana y disposición genuina de escuchar y apoyar.</p>
            </div>

            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg"><i class="fa-solid fa-scale-balanced"></i></div>
              <h4 class="font-extrabold text-slate-900">Responsabilidad Democrática</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Promovemos la participación libre, consciente y ordenada como base de una sociedad justa y próspera.</p>
            </div>

            <div class="surface-sub p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
              <div class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-lg"><i class="fa-solid fa-chart-line"></i></div>
              <h4 class="font-extrabold text-slate-900">Innovación Organizativa</h4>
              <p class="text-xs text-slate-600 leading-relaxed">Utilizamos herramientas digitales modernas para medir metas, fortalecer recintos y tomar mejores decisiones.</p>
            </div>
          </div>
        </div>
      </section>

    </main>
  </div>

  <!-- ===================================================================== -->
  <!-- MODAL: INSCRIBIR / EDITAR PERSONA (AFILIADO) EN EL PADRÓN             -->
  <!-- ===================================================================== -->
  <div id="modalForm" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white text-slate-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200 my-auto">
      <div class="bg-indigo-950 text-white px-6 py-4 flex justify-between items-center">
        <h4 id="modalTitulo" class="text-base font-extrabold flex items-center gap-2">
          <i class="fa-solid fa-user-plus text-amber-400"></i> Inscribir Miembros (Militante)
        </h4>
        <button onclick="cerrarModal()" class="text-indigo-300 hover:text-white text-2xl font-bold leading-none">&times;</button>
      </div>
      <form onsubmit="guardarIntegrante(event)" class="p-6 space-y-4 text-sm max-h-[82vh] overflow-y-auto">
        <input type="hidden" id="formId">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Cédula de Identidad / DNI *</label>
          <input type="text" id="formCedula" required placeholder="001-0000000-0" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Nombres y Apellidos Completos *</label>
          <input type="text" id="formNombre" required placeholder="Ej: Juan Pablo Duarte" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Teléfono / WhatsApp *</label>
            <input type="text" id="formTelefono" required placeholder="809-555-0101" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Mesa Electoral *</label>
            <input type="text" id="formMesaElectoral" required placeholder="Ej: Mesa 04 / Colegio 0123" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Centro de Votación / Dirección *</label>
          <input type="text" id="formCentroDireccion" required placeholder="Ej: Escuela Primaria Central, Av. Independencia #120" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Coordinador Responsable *</label>
            <select id="formCoordinador" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none"></select>
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Mes de Afiliación *</label>
            <select id="formMes" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
              <option value="Ene">Enero</option>
              <option value="Feb">Febrero</option>
              <option value="Mar">Marzo</option>
              <option value="Abr">Abril</option>
              <option value="May">Mayo</option>
              <option value="Jun" selected>Junio (Actual)</option>
            </select>
          </div>
        </div>
        <p id="msgCoordBloqueado" class="text-xs text-amber-700 bg-amber-50 p-2.5 rounded-xl border border-amber-200 hidden">
          <i class="fa-solid fa-lock mr-1"></i> Como Coordinador, el afiliado se inscribe directamente bajo su coordinación territorial.
        </p>
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
          <button type="button" onclick="cerrarModal()" class="btn-modern bg-slate-200 hover:bg-slate-300 text-slate-800">
            Cancelar
          </button>
          <button type="submit" class="btn-modern bg-indigo-600 hover:bg-indigo-700 text-white">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Persona
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ===================================================================== -->
  <!-- MODAL: AGREGAR / EDITAR COORDINADOR (Directora María y Administrador) -->
  <!-- ===================================================================== -->
  <div id="modalCoordinador" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white text-slate-800 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden border border-slate-200">
      <div class="bg-indigo-950 text-white px-6 py-4 flex justify-between items-center">
        <h4 id="modalCoordTitulo" class="text-base font-extrabold flex items-center gap-2">
          <i class="fa-solid fa-user-tie text-amber-400"></i> Registrar Nuevo Coordinador
        </h4>
        <button onclick="cerrarModalCoordinador()" class="text-indigo-300 hover:text-white text-2xl font-bold leading-none">&times;</button>
      </div>
      <form onsubmit="guardarCoordinador(event)" class="p-6 space-y-4 text-sm">
        <input type="hidden" id="coordFormId">
        <div>
          <label class="block font-bold text-slate-700 mb-1">Nombre y Apellido del Coordinador *</label>
          <input type="text" id="coordFormNombre" required placeholder="Ej: Luis Alberto Ramírez" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
          <div>
            <label class="block font-bold text-slate-700 mb-1">Zona / Demarcación *</label>
            <input type="text" id="coordFormZona" required placeholder="Ej: Zona Oeste / Distrito 1" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
          <div>
            <label class="block font-bold text-slate-700 mb-1">Cédula de Identidad *</label>
            <input type="text" id="coordFormCedula" required minlength="4" oninput="actualizarPreviewClaveCoord()" placeholder="001-3344556-9" class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
          </div>
        </div>
        <div class="p-3.5 rounded-2xl bg-indigo-50 border border-indigo-200 text-xs text-indigo-950 flex items-center justify-between">
          <div>
            <span class="font-extrabold block"><i class="fa-solid fa-key text-indigo-600 mr-1"></i> Credencial de Acceso Automática:</span>
            <span>La contraseña inicial serán los últimos 4 dígitos de su cédula.</span>
          </div>
          <span id="previewClaveCoord" class="font-mono font-black text-base bg-amber-400 text-indigo-950 px-3 py-1 rounded-xl">----</span>
        </div>
        <div id="wrapPermisoAdminCoord" class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-3">
          <div>
            <label for="coordFormEsAdmin" class="font-extrabold text-slate-900 text-xs block cursor-pointer">
              <i class="fa-solid fa-shield-halved text-amber-600 mr-1"></i> Otorgar Permiso de Administrador
            </label>
            <span class="text-[11px] text-slate-600">Si se activa, este coordinador tendrá acceso total (Pase de Lista, Dashboard y gestión).</span>
          </div>
          <input type="checkbox" id="coordFormEsAdmin" class="w-5 h-5 accent-indigo-600 cursor-pointer shrink-0">
        </div>
        <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
          <button type="button" onclick="cerrarModalCoordinador()" class="btn-modern bg-slate-200 hover:bg-slate-300 text-slate-800">
            Cancelar
          </button>
          <button type="submit" class="btn-modern bg-indigo-600 hover:bg-indigo-700 text-white">
            <i class="fa-solid fa-floppy-disk"></i> Guardar Coordinador
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: CAMBIAR CONTRASEÑA -->
  <div id="modalCambiarPass" class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white text-slate-800 rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-200">
      <div class="bg-indigo-950 text-white px-6 py-4 flex justify-between items-center">
        <h4 class="text-base font-extrabold flex items-center gap-2">
          <i class="fa-solid fa-key text-amber-400"></i> Actualizar Contraseña de Acceso
        </h4>
        <button onclick="cerrarModalCambiarPass()" class="text-indigo-300 hover:text-white text-2xl font-bold leading-none">&times;</button>
      </div>
      <form onsubmit="guardarNuevaPassword(event)" class="p-6 space-y-4 text-sm">
        <div id="bannerPrimerInicio" class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed">
          <strong><i class="fa-solid fa-shield-halved mr-1"></i> Primer inicio de sesión detectado:</strong> Su contraseña actual corresponde a los últimos 4 dígitos de su cédula. Por seguridad, puede personalizar su contraseña ahora o conservarla.
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Nueva Contraseña *</label>
          <div class="relative">
            <input type="password" id="inputNuevaPass" required minlength="4" placeholder="Mínimo 4 caracteres"
              class="w-full p-3 pr-10 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
            <button type="button" onclick="toggleVerPassword('inputNuevaPass', 'iconEyeNewPass')" class="absolute right-3 top-3 text-slate-400 hover:text-indigo-600">
              <i id="iconEyeNewPass" class="fa-solid fa-eye"></i>
            </button>
          </div>
        </div>
        <div>
          <label class="block font-bold text-slate-700 mb-1">Confirmar Nueva Contraseña *</label>
          <input type="password" id="inputConfirmarPass" required minlength="4" placeholder="Repita la nueva contraseña"
            class="w-full p-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-600 focus:outline-none">
        </div>
        <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
          <button type="button" onclick="cerrarModalCambiarPass()" class="btn-modern bg-slate-200 hover:bg-slate-300 text-slate-700 !py-2.5 !px-4 !text-xs">
            Más tarde
          </button>
          <button type="submit" class="btn-modern bg-indigo-600 hover:bg-indigo-700 text-white !py-2.5 !px-5 !text-xs">
            <i class="fa-solid fa-check"></i> Actualizar Contraseña
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- MODAL: FORMULARIO OFICIAL DE INSCRIPCIÓN DE MIEMBROS POR COORDINADOR / USUARIO -->
  <div id="modalFormularioMiembros" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-3 sm:p-6 overflow-y-auto">
    <div class="bg-white text-slate-900 rounded-3xl shadow-2xl max-w-4xl w-full overflow-hidden border-2 border-amber-400 my-auto">
      <!-- Barra superior del modal -->
      <div class="bg-blue-950 text-white px-5 py-3 flex flex-wrap justify-between items-center gap-3">
        <div class="flex items-center gap-2 text-sm font-extrabold">
          <i class="fa-solid fa-clipboard-list text-amber-400"></i>
          <span>Formulario Oficial de Inscripción de Miembros</span>
        </div>
        <div class="flex items-center gap-2">
          <button type="button" onclick="imprimirFormularioMiembros()" class="px-3.5 py-1.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-blue-950 font-extrabold text-xs flex items-center gap-1.5 shadow">
            <i class="fa-solid fa-print"></i> Imprimir Formulario
          </button>
          <button type="button" onclick="cerrarModalFormularioMiembros()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white font-bold flex items-center justify-center">
            ✕
          </button>
        </div>
      </div>

      <!-- Contenido idéntico al formulario de la imagen -->
      <div id="formularioMiembrosContenido" class="p-4 sm:p-6 space-y-3 bg-white max-h-[82vh] overflow-y-auto">
        <!-- Encabezado gráfico con la imagen oficial -->
        <div class="rounded-2xl overflow-hidden border-2 border-blue-900 shadow-sm bg-gradient-to-b from-sky-100 to-white">
          <img src="formulario-maria-castillo.jpg" alt="Yo Estoy con la Profesora María Castillo 2028/2032 PRM" class="w-full h-auto object-contain">
        </div>

        <!-- Franja: DATOS DEL COORDINADOR/A DEL EQUIPO -->
        <div class="rounded-xl overflow-hidden border-2 border-blue-900 shadow-sm">
          <div class="bg-blue-950 text-white text-center text-xs font-black uppercase tracking-wider py-1.5 px-3">
            DATOS DEL COORDINADOR/A DEL EQUIPO
          </div>
          <div class="bg-sky-50/70 px-4 py-2.5 grid grid-cols-1 md:grid-cols-3 gap-2 text-xs text-blue-950 font-bold">
            <div class="truncate">
              <span class="uppercase text-slate-600">Nombre del Coordinador/a:</span>
              <span id="formCoordNombre" class="text-blue-900 font-black ml-1 underline">---</span>
            </div>
            <div class="truncate">
              <span class="uppercase text-slate-600">Cédula:</span>
              <span id="formCoordCedula" class="text-blue-900 font-black ml-1 underline font-mono">---</span>
            </div>
            <div class="truncate">
              <span class="uppercase text-slate-600">Teléfono / Zona:</span>
              <span id="formCoordTelefono" class="text-blue-900 font-black ml-1 underline">---</span>
            </div>
          </div>
        </div>

        <!-- Tabla numerada estilo Formulario Oficial (1..10+) -->
        <div class="rounded-2xl overflow-hidden border-2 border-blue-900 shadow-sm">
          <div class="overflow-x-auto touch-scroll">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
              <thead>
                <tr class="bg-gradient-to-r from-sky-200 via-blue-100 to-sky-200 text-blue-950 border-b-2 border-amber-400 font-black uppercase text-xs">
                  <th class="p-2.5 border-r border-blue-800/30 w-10 text-center">#</th>
                  <th class="p-2.5 border-r border-blue-800/30"><i class="fa-solid fa-user mr-1 text-blue-900"></i> NOMBRE</th>
                  <th class="p-2.5 border-r border-blue-800/30"><i class="fa-solid fa-id-card mr-1 text-blue-900"></i> CÉDULA</th>
                  <th class="p-2.5 border-r border-blue-800/30"><i class="fa-solid fa-phone mr-1 text-blue-900"></i> TELÉFONO</th>
                  <th class="p-2.5 border-r border-blue-800/30"><i class="fa-solid fa-school mr-1 text-blue-900"></i> CENTRO DE VOTACIÓN / DIRECCIÓN</th>
                  <th class="p-2.5"><i class="fa-solid fa-box-archive mr-1 text-blue-900"></i> MESA ELECTORAL</th>
                </tr>
              </thead>
              <tbody id="tablaFormularioMiembrosBody" class="divide-y divide-blue-900/20 text-slate-800"></tbody>
            </table>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 pt-2">
          <div>
            <span id="formResumenMiembros" class="font-bold text-blue-950 block">Total de miembros inscritos: 0</span>
            <span id="formHojaIndicador" class="text-[11px] font-bold text-indigo-700">Hoja 1 (Militantes 1 - 10)</span>
          </div>
          <div class="flex flex-wrap items-center gap-2 no-print">
            <button type="button" onclick="agregarMilitanteDesdeFormulario()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow">
              <i class="fa-solid fa-user-plus"></i> Inscribir Militante
            </button>
            <button type="button" id="btnFormAnterior" onclick="cambiarPaginaFormulario(-1)" class="px-3.5 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-extrabold text-xs flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed">
              <i class="fa-solid fa-arrow-left"></i> Anterior
            </button>
            <button type="button" id="btnFormSiguiente" onclick="cambiarPaginaFormulario(1)" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold text-xs flex items-center gap-1.5 shadow">
              <span>Siguiente (10 más)</span> <i class="fa-solid fa-arrow-right"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ÁREA DE IMPRESIÓN -->
  <div id="printArea" class="hidden"></div>

  <script>
    const API_BASE = "api";
    let modoConectado = false;

    // Coordinadores iniciales por defecto
    const coordinadoresDefault = {
      coord_1: { id: "coord_1", nombre: "Carlos Santana", zona: "Zona Norte", cedula: "402-9876543-1" },
      coord_2: { id: "coord_2", nombre: "Rosa Gómez", zona: "Zona Sur", cedula: "001-2345678-9" },
      coord_3: { id: "coord_3", nombre: "Roberto Almonte", zona: "Zona Este", cedula: "001-8765432-4" }
    };

    let coordinadores = JSON.parse(localStorage.getItem("padron_coordinadores_v2")) || coordinadoresDefault;

    function guardarCoordinadoresDB() {
      localStorage.setItem("padron_coordinadores_v2", JSON.stringify(coordinadores));
    }

    function obtenerUltimos4Cedula(cedula) {
      const soloDigitos = (cedula || "").replace(/\D/g, "");
      return soloDigitos.slice(-4) || "0000";
    }

    // Verificar si el rol actual tiene privilegios ejecutivos totales (Administrador, Directora María Castillo, Coordinador de Campaña o Coordinador con permiso de Admin)
    function tieneControlTotal() {
      if (rolActual === "admin" || rolActual === "directora" || rolActual === "campana") return true;
      if (rolActual && coordinadores[rolActual] && coordinadores[rolActual].esAdmin) return true;
      if (usuarioAutenticado && usuarioAutenticado.esAdmin) return true;
      return false;
    }

    const usuariosDefault = [
      {
        id: "u_admin",
        username: "Administrador",
        aliases: ["admin", "administrador"],
        nombreCompleto: "Administrador General del Sistema",
        rol: "admin",
        rolLabel: "Administrador",
        cedula: "001-1234567-8",
        password: obtenerUltimos4Cedula("001-1234567-8"), // 5678
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      },
      {
        id: "u_directora",
        username: "Directora Maria Castillo",
        aliases: ["directora", "directora maria castillo", "directora maría castillo", "maria castillo", "maría castillo", "maria", "alcaldesa", "arcardesa"],
        nombreCompleto: "Directora Maria Castillo",
        rol: "directora",
        rolLabel: "Directora Maria Castillo",
        cedula: "001-4567890-2",
        password: obtenerUltimos4Cedula("001-4567890-2"), // 8902
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      },
      {
        id: "u_campana",
        username: "Robert Ritz M. Roque Rivas",
        aliases: ["robert", "robert rivas", "robert ritz", "roque rivas", "robert ritz m. roque rivas", "robert ritz.m roque rivas", "coordinador de campaña", "campana"],
        nombreCompleto: "Robert Ritz M. Roque Rivas",
        rol: "campana",
        rolLabel: "Coordinador de Campaña",
        cedula: "001-7788990-5",
        password: obtenerUltimos4Cedula("001-7788990-5"), // 8905
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      },
      {
        id: "u_coord1",
        username: "Coordinador",
        aliases: ["coordinador", "carlos santana", "carlos", "coord_1"],
        nombreCompleto: "Carlos Santana (Coordinador Zona Norte)",
        rol: "coord_1",
        rolLabel: "Coordinador",
        cedula: "402-9876543-1",
        password: obtenerUltimos4Cedula("402-9876543-1"), // 5431
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      },
      {
        id: "u_coord2",
        username: "Rosa Gomez",
        aliases: ["rosa gomez", "rosa gómez", "rosa", "coord_2"],
        nombreCompleto: "Rosa Gómez (Coordinadora Zona Sur)",
        rol: "coord_2",
        rolLabel: "Coordinador",
        cedula: "001-2345678-9",
        password: obtenerUltimos4Cedula("001-2345678-9"), // 6789
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      },
      {
        id: "u_coord3",
        username: "Roberto Almonte",
        aliases: ["roberto almonte", "roberto", "coord_3"],
        nombreCompleto: "Roberto Almonte (Coordinador Zona Este)",
        rol: "coord_3",
        rolLabel: "Coordinador",
        cedula: "001-8765432-4",
        password: obtenerUltimos4Cedula("001-8765432-4"), // 4324
        intentosFallidos: 0,
        bloqueado: false,
        primerLogin: true
      }
    ];

    let usuarios = JSON.parse(localStorage.getItem("padron_usuarios_v2")) || usuariosDefault;
    // Migrar perfil legado "Alcaldesa" / "u_alcaldesa" -> "u_directora" (Directora Maria Castillo)
    const legacyIdx = usuarios.findIndex(u => u.id === "u_alcaldesa" || u.username === "Alcaldesa" || (u.cedula === "001-4567890-2" && u.id !== "u_directora"));
    if (legacyIdx !== -1) {
      const defDir = usuariosDefault.find(d => d.id === "u_directora");
      usuarios[legacyIdx] = { ...usuarios[legacyIdx], ...defDir, password: usuarios[legacyIdx].password || defDir.password };
      usuarios = usuarios.filter((u, i) => !(u.id === "u_directora" && i !== legacyIdx));
    }
    // Asegurar que nuevos roles por defecto (como Coordinador de Campaña y Directora) se agreguen o actualicen si ya existía localStorage
    usuariosDefault.forEach(defUser => {
      const idx = usuarios.findIndex(u => u.id === defUser.id);
      if (idx === -1) {
        usuarios.push(defUser);
      } else if (defUser.id === "u_campana" || defUser.id === "u_directora") {
        usuarios[idx].username = defUser.username;
        usuarios[idx].nombreCompleto = defUser.nombreCompleto;
        usuarios[idx].aliases = defUser.aliases;
        usuarios[idx].rolLabel = defUser.rolLabel;
      }
    });
    localStorage.setItem("padron_usuarios_v2", JSON.stringify(usuarios));
    let bitacoraAccesos = JSON.parse(localStorage.getItem("padron_bitacora_v2")) || [];
    let usuarioAutenticado = null;
    let rolActual = null;

    const datosIniciales = [
      { id: 101, folio: "PAD-2026-101", cedula: "001-1458923-1", nombre: "Alberto Mejía Díaz", telefono: "809-555-1201", recinto: "Escuela Central", centroVotacion: "Escuela Primaria Central", direccionCentro: "Calle Principal #12, Zona Norte", mesaElectoral: "Mesa 01", coordId: "coord_1", mes: "Ene", fecha: "15/01/2026" },
      { id: 102, folio: "PAD-2026-102", cedula: "001-2093847-5", nombre: "Carmen Lidia Tavárez", telefono: "809-555-4321", recinto: "Colegio San Judas", centroVotacion: "Colegio San Judas Tadeo", direccionCentro: "Av. Libertad #45, Zona Norte", mesaElectoral: "Mesa 03", coordId: "coord_1", mes: "Feb", fecha: "10/02/2026" },
      { id: 103, folio: "PAD-2026-103", cedula: "001-0892711-2", nombre: "Marcos Aurelio Guzmán", telefono: "829-555-8899", recinto: "Liceo Unión", centroVotacion: "Liceo Unión Panamericana", direccionCentro: "Calle Duarte #88, Zona Sur", mesaElectoral: "Mesa 02", coordId: "coord_2", mes: "Feb", fecha: "18/02/2026" },
      { id: 104, folio: "PAD-2026-104", cedula: "001-4492019-3", nombre: "Darío José Fernández", telefono: "809-555-7766", recinto: "Club San Carlos", centroVotacion: "Club Deportivo San Carlos", direccionCentro: "Av. México #101, Zona Este", mesaElectoral: "Mesa 05", coordId: "coord_3", mes: "Mar", fecha: "05/03/2026" },
      { id: 105, folio: "PAD-2026-105", cedula: "001-7748291-4", nombre: "Lucía Mercedes Peña", telefono: "809-555-9012", recinto: "Escuela Central", centroVotacion: "Escuela Primaria Central", direccionCentro: "Calle Principal #12, Zona Norte", mesaElectoral: "Mesa 01", coordId: "coord_1", mes: "Mar", fecha: "22/03/2026" },
      { id: 106, folio: "PAD-2026-106", cedula: "402-3319284-7", nombre: "Héctor Rafael Domínguez", telefono: "829-555-3412", recinto: "Politécnico Norte", centroVotacion: "Politécnico del Norte", direccionCentro: "Carretera Vieja Km 4, Zona Norte", mesaElectoral: "Mesa 04", coordId: "coord_1", mes: "Abr", fecha: "11/04/2026" },
      { id: 107, folio: "PAD-2026-107", cedula: "001-9928172-6", nombre: "Valeria Sofía Reyes", telefono: "849-555-6633", recinto: "Liceo Unión", centroVotacion: "Liceo Unión Panamericana", direccionCentro: "Calle Duarte #88, Zona Sur", mesaElectoral: "Mesa 02", coordId: "coord_2", mes: "May", fecha: "04/05/2026" },
      { id: 108, folio: "PAD-2026-108", cedula: "001-6619283-0", nombre: "Fernando Emilio Castro", telefono: "809-555-3190", recinto: "Escuela Central", centroVotacion: "Escuela Primaria Central", direccionCentro: "Calle Principal #12, Zona Norte", mesaElectoral: "Mesa 03", coordId: "coord_1", mes: "Jun", fecha: "02/06/2026" },
      { id: 109, folio: "PAD-2026-109", cedula: "402-5581920-8", nombre: "Patricia Elena Vargas", telefono: "829-555-7122", recinto: "Liceo Unión", centroVotacion: "Liceo Unión Panamericana", direccionCentro: "Calle Duarte #88, Zona Sur", mesaElectoral: "Mesa 06", coordId: "coord_2", mes: "Jun", fecha: "09/06/2026" },
      { id: 110, folio: "PAD-2026-110", cedula: "001-3194827-2", nombre: "Gabriel Antonio Mora", telefono: "809-555-8411", recinto: "Club San Carlos", centroVotacion: "Club Deportivo San Carlos", direccionCentro: "Av. México #101, Zona Este", mesaElectoral: "Mesa 05", coordId: "coord_3", mes: "Jun", fecha: "12/06/2026" }
    ];

    let datos = JSON.parse(localStorage.getItem("padron_partido_db")) || datosIniciales;
    datos = datos.map(d => {
      const centroDir = d.centroDireccion || [d.centroVotacion || d.recinto, d.direccionCentro].filter(Boolean).join(" — ");
      return {
        ...d,
        centroDireccion: centroDir,
        mesaElectoral: d.mesaElectoral || "",
        mes: d.mes || "Jun",
        fecha: d.fecha || "01/06/2026"
      };
    });

    let chartPieInstance = null;
    let chartBarInstance = null;
    let chartTrendInstance = null;

    // Renderizar tarjetas de acceso rápido en la pantalla de Login
    function renderCredencialesLogin() {
      const cont = document.getElementById("listaCredencialesDemo");
      if (!cont) return;
      cont.innerHTML = usuarios.map(u => `
        <button type="button" onclick="autocompletarLogin('${u.username}', '${u.password}')" class="w-full text-left p-2 rounded-xl bg-indigo-900/70 hover:bg-indigo-800 transition flex justify-between items-center border border-indigo-700/50">
          <div>
            <span class="font-bold text-white block">${u.username} <span class="text-[10px] text-amber-300">(${u.rolLabel})</span></span>
            <span class="text-[10px] text-indigo-300">Cédula: ${u.cedula}</span>
          </div>
          <span class="bg-amber-400 text-indigo-950 font-mono font-black px-2 py-0.5 rounded text-xs">${u.password}</span>
        </button>
      `).join("");
    }

    // Poblar dinámicamente todos los selectores y checkboxes de coordinadores
    function actualizarSelectoresCoordinadores() {
      const listaCoords = Object.values(coordinadores);

      // 1. Selector de filtro en Padrón
      const filtro = document.getElementById("filtroCoordinador");
      if (filtro) {
        const valPrevio = filtro.value || "todos";
        filtro.innerHTML = `<option value="todos">Todos los Coordinadores</option>` +
          listaCoords.map(c => `<option value="${c.id}">${c.nombre} (${c.zona})</option>`).join("");
        if (valPrevio === "todos" || coordinadores[valPrevio]) {
          filtro.value = valPrevio;
        }
      }

      // 2. Selector del formulario de Inscribir Persona
      const selectForm = document.getElementById("formCoordinador");
      if (selectForm) {
        const valFormPrev = selectForm.value;
        selectForm.innerHTML = listaCoords.map(c => `<option value="${c.id}">${c.nombre} (${c.zona})</option>`).join("");
        if (coordinadores[valFormPrev]) {
          selectForm.value = valFormPrev;
        }
      }

      // 3. Checkboxes del Comparador
      const contChecks = document.getElementById("contenedorChecksComparador");
      if (contChecks) {
        contChecks.innerHTML = listaCoords.map(c => `
          <label class="flex items-center gap-2 text-sm font-bold cursor-pointer px-3 py-1.5 rounded-xl bg-white shadow-xs">
            <input type="checkbox" class="chk-comparar w-4 h-4 accent-indigo-600" value="${c.id}" checked onchange="actualizarComparador()">
            <span>${c.nombre} (${c.zona})</span>
          </label>
        `).join("");
      }
    }

    function aplicarColorFondoGuardado() {
      const temaGuardado = localStorage.getItem("padron_bg_theme") || "gris";
      cambiarColorFondo(temaGuardado, false);
    }

    function cambiarColorFondo(color, cerrarMenu = true) {
      const clasesTema = ["theme-azul", "theme-verde", "theme-gris", "theme-blanco", "theme-negro", "theme-morado"];
      document.body.classList.remove(...clasesTema);
      document.body.classList.add("theme-" + color);
      localStorage.setItem("padron_bg_theme", color);
      if (cerrarMenu) {
        document.getElementById("menuColores").classList.add("hidden");
      }
      if (usuarioAutenticado) {
        actualizarGraficos();
        if (tieneControlTotal()) {
          actualizarGraficosAsistencia();
        }
      }
    }

    function toggleMenuColores() {
      document.getElementById("menuColores").classList.toggle("hidden");
    }

    function guardarUsuarios() {
      localStorage.setItem("padron_usuarios_v2", JSON.stringify(usuarios));
      renderCredencialesLogin();
    }

    function registrarEventoBitacora(username, rolLabel, estado) {
      const ahora = new Date();
      const fechaHora = ahora.toLocaleDateString("es-DO") + " " + ahora.toLocaleTimeString("es-DO");
      bitacoraAccesos.unshift({
        fechaHora,
        username,
        rolLabel,
        estado
      });
      localStorage.setItem("padron_bitacora_v2", JSON.stringify(bitacoraAccesos.slice(0, 100)));
    }

    function autocompletarLogin(user, pass) {
      document.getElementById("loginUsername").value = user;
      document.getElementById("loginPassword").value = pass;
      document.getElementById("loginErrorBox").classList.add("hidden");
    }

    function toggleVerPassword(inputId, iconId) {
      const input = document.getElementById(inputId);
      const icon = document.getElementById(iconId);
      if (input.type === "password") {
        input.type = "text";
        icon.classList.replace("fa-eye", "fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.replace("fa-eye-slash", "fa-eye");
      }
    }

    function mostrarErrorLogin(titulo, mensaje) {
      const box = document.getElementById("loginErrorBox");
      document.getElementById("loginErrorTitle").innerText = titulo;
      document.getElementById("loginErrorMsg").innerText = mensaje;
      box.classList.remove("hidden");
    }

    async function procesarLogin(e) {
      e.preventDefault();
      const inputUser = document.getElementById("loginUsername").value.trim().toLowerCase();
      const inputPass = document.getElementById("loginPassword").value.trim();

      // Validación con Servidor MySQL de Hostinger si está activo
      if (modoConectado) {
        try {
          const res = await fetch(`${API_BASE}/auth.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "login", username: inputUser, password: inputPass })
          });
          const json = await res.json();
          if (json.success && json.user) {
            usuarioAutenticado = json.user;
            rolActual = json.user.rol;

            document.getElementById("loginErrorBox").classList.add("hidden");
            document.getElementById("loginScreen").classList.add("hidden");
            document.getElementById("appContainer").classList.remove("hidden");

            configurarInterfazPorRol();
            if (json.user.primerLogin) {
              abrirModalCambiarPass(true);
            }
            return;
          } else {
            mostrarErrorLogin(json.title || "Error de Acceso", json.error || "Credenciales incorrectas.");
            return;
          }
        } catch (err) {
          console.warn("Fallo validación en servidor, probando modo local:", err);
        }
      }

      // Lógica local fallback
      const userObj = usuarios.find(u =>
        u.username.toLowerCase() === inputUser ||
        (u.aliases && u.aliases.includes(inputUser))
      );

      if (!userObj) {
        registrarEventoBitacora(inputUser || "Desconocido", "N/A", "Fallido: Usuario no existe");
        mostrarErrorLogin("Usuario no encontrado", "El nombre de usuario ingresado no corresponde a ningún rol registrado.");
        return;
      }

      if (userObj.bloqueado || userObj.intentosFallidos >= 3) {
        userObj.bloqueado = true;
        guardarUsuarios();
        registrarEventoBitacora(userObj.username, userObj.rolLabel, "Bloqueado: Superó los 3 intentos fallidos");
        mostrarErrorLogin(
          "Cuenta Bloqueada por Seguridad",
          `El usuario "${userObj.username}" ha superado el límite máximo de 3 intentos fallidos. Solicite a la Directora o al Administrador restablecer su contraseña.`
        );
        return;
      }

      if (userObj.password !== inputPass) {
        userObj.intentosFallidos += 1;
        const restantes = 3 - userObj.intentosFallidos;
        if (restantes <= 0) {
          userObj.bloqueado = true;
          guardarUsuarios();
          registrarEventoBitacora(userObj.username, userObj.rolLabel, "Cuenta bloqueada tras 3er intento fallido");
          mostrarErrorLogin(
            "Cuenta Bloqueada (3/3 intentos fallidos)",
            `Ha agotado los 3 intentos permitidos para "${userObj.username}". El acceso ha sido bloqueado por seguridad.`
          );
        } else {
          guardarUsuarios();
          registrarEventoBitacora(userObj.username, userObj.rolLabel, `Contraseña incorrecta (${userObj.intentosFallidos}/3 intentos)`);
          mostrarErrorLogin(
            "Contraseña Incorrecta",
            `Las credenciales no coinciden. Le quedan ${restantes} intento(s) antes del bloqueo de seguridad.`
          );
        }
        return;
      }

      userObj.intentosFallidos = 0;
      userObj.bloqueado = false;
      const ahora = new Date();
      const fechaHoraStr = ahora.toLocaleDateString("es-DO") + " a las " + ahora.toLocaleTimeString("es-DO");
      userObj.ultimoAcceso = fechaHoraStr;
      guardarUsuarios();

      registrarEventoBitacora(userObj.username, userObj.rolLabel, "Acceso Exitoso");

      usuarioAutenticado = userObj;
      rolActual = userObj.rol;

      document.getElementById("loginErrorBox").classList.add("hidden");
      document.getElementById("loginScreen").classList.add("hidden");
      document.getElementById("appContainer").classList.remove("hidden");

      configurarInterfazPorRol();

      if (userObj.primerLogin) {
        abrirModalCambiarPass(true);
      }
    }

    function cerrarSesion() {
      if (usuarioAutenticado) {
        registrarEventoBitacora(usuarioAutenticado.username, usuarioAutenticado.rolLabel, "Cierre de sesión manual");
      }
      usuarioAutenticado = null;
      rolActual = null;
      document.getElementById("loginPassword").value = "";
      document.getElementById("appContainer").classList.add("hidden");
      document.getElementById("loginScreen").classList.remove("hidden");
      renderCredencialesLogin();
    }

    async function desbloquearEmergencia() {
      if (modoConectado) {
        try {
          await fetch(`${API_BASE}/auth.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ action: "reset_emergency" })
          });
        } catch (err) {
          console.error("Error al restaurar en servidor:", err);
        }
      }
      usuarios = usuarios.map(u => ({
        ...u,
        intentosFallidos: 0,
        bloqueado: false,
        password: obtenerUltimos4Cedula(u.cedula)
      }));
      guardarUsuarios();
      mostrarErrorLogin("Cuentas Restauradas", "Se han desbloqueado todos los usuarios y restablecido sus contraseñas iniciales (últimos 4 dígitos de su cédula).");
    }

    function abrirModalCambiarPass(esPrimerInicio) {
      const banner = document.getElementById("bannerPrimerInicio");
      if (esPrimerInicio) {
        banner.classList.remove("hidden");
      } else {
        banner.classList.add("hidden");
      }
      document.getElementById("inputNuevaPass").value = "";
      document.getElementById("inputConfirmarPass").value = "";
      document.getElementById("modalCambiarPass").classList.remove("hidden");
    }

    function cerrarModalCambiarPass() {
      if (usuarioAutenticado) {
        usuarioAutenticado.primerLogin = false;
        guardarUsuarios();
      }
      document.getElementById("modalCambiarPass").classList.add("hidden");
    }

    function guardarNuevaPassword(e) {
      e.preventDefault();
      const nueva = document.getElementById("inputNuevaPass").value.trim();
      const confirmar = document.getElementById("inputConfirmarPass").value.trim();
      if (nueva !== confirmar) {
        alert("Las contraseñas ingresadas no coinciden. Por favor verifique.");
        return;
      }
      if (usuarioAutenticado) {
        const idx = usuarios.findIndex(u => u.id === usuarioAutenticado.id);
        if (idx !== -1) {
          usuarios[idx].password = nueva;
          usuarios[idx].primerLogin = false;
          usuarioAutenticado = usuarios[idx];
          guardarUsuarios();
          registrarEventoBitacora(usuarioAutenticado.username, usuarioAutenticado.rolLabel, "Cambió su contraseña personal");
          renderSeguridadYBitacora();
        }
      }
      document.getElementById("modalCambiarPass").classList.add("hidden");
      alert("Su contraseña ha sido actualizada exitosamente.");
    }

    function adminRestablecerPassword(userId) {
      if (!tieneControlTotal()) return;
      const u = usuarios.find(x => x.id === userId);
      if (!u) return;
      const claveInicial = obtenerUltimos4Cedula(u.cedula);
      if (confirm(`¿Restablecer la contraseña de "${u.username}" a los últimos 4 dígitos de su cédula (${claveInicial}) y desbloquear su cuenta?`)) {
        u.password = claveInicial;
        u.intentosFallidos = 0;
        u.bloqueado = false;
        u.primerLogin = true;
        guardarUsuarios();
        registrarEventoBitacora(usuarioAutenticado.username, usuarioAutenticado.rolLabel, `Restableció clave de ${u.username} a ${claveInicial}`);
        renderSeguridadYBitacora();
      }
    }

    // =========================================================================
    // GESTIÓN DE COORDINADORES (Directora María Castillo y Administrador)
    // =========================================================================
    function abrirModalNuevoCoordinador() {
      if (!tieneControlTotal()) {
        alert("Solo la Directora María Castillo, el Coordinador de Campaña y el Administrador pueden agregar nuevos coordinadores.");
        return;
      }
      document.getElementById("coordFormId").value = "";
      document.getElementById("coordFormNombre").value = "";
      document.getElementById("coordFormZona").value = "";
      document.getElementById("coordFormCedula").value = "";
      document.getElementById("coordFormEsAdmin").checked = false;
      document.getElementById("previewClaveCoord").innerText = "----";
      document.getElementById("modalCoordTitulo").innerHTML = `<i class="fa-solid fa-user-tie text-amber-400"></i> Registrar Nuevo Coordinador`;
      document.getElementById("modalCoordinador").classList.remove("hidden");
    }

    function editarCoordinador(coordId) {
      if (!tieneControlTotal()) return;
      const c = coordinadores[coordId];
      if (!c) return;
      document.getElementById("coordFormId").value = c.id;
      document.getElementById("coordFormNombre").value = c.nombre;
      document.getElementById("coordFormZona").value = c.zona;
      document.getElementById("coordFormCedula").value = c.cedula || "";
      document.getElementById("coordFormEsAdmin").checked = Boolean(c.esAdmin);
      actualizarPreviewClaveCoord();
      document.getElementById("modalCoordTitulo").innerHTML = `<i class="fa-solid fa-user-pen text-amber-400"></i> Modificar Coordinador`;
      document.getElementById("modalCoordinador").classList.remove("hidden");
    }

    function cerrarModalCoordinador() {
      document.getElementById("modalCoordinador").classList.add("hidden");
    }

    function actualizarPreviewClaveCoord() {
      const ced = document.getElementById("coordFormCedula").value;
      const digitos = (ced || "").replace(/\D/g, "");
      document.getElementById("previewClaveCoord").innerText = digitos.length >= 4 ? digitos.slice(-4) : "----";
    }

    async function guardarCoordinador(e) {
      e.preventDefault();
      if (!tieneControlTotal()) return;

      const idExistente = document.getElementById("coordFormId").value;
      const nombre = document.getElementById("coordFormNombre").value.trim();
      const zona = document.getElementById("coordFormZona").value.trim();
      const cedula = document.getElementById("coordFormCedula").value.trim();
      const esAdmin = document.getElementById("coordFormEsAdmin").checked;
      const claveInicial = obtenerUltimos4Cedula(cedula);
      const operador = usuarioAutenticado ? usuarioAutenticado.username : "Admin";
      const operadorRol = usuarioAutenticado ? usuarioAutenticado.rolLabel : "Administrador";

      if (idExistente && coordinadores[idExistente]) {
        coordinadores[idExistente].nombre = nombre;
        coordinadores[idExistente].zona = zona;
        coordinadores[idExistente].cedula = cedula;
        coordinadores[idExistente].esAdmin = esAdmin;

        const uAsociado = usuarios.find(u => u.rol === idExistente);
        if (uAsociado) {
          uAsociado.username = nombre;
          uAsociado.nombreCompleto = `${nombre} (Coordinador ${zona})`;
          uAsociado.cedula = cedula;
          uAsociado.esAdmin = esAdmin;
          uAsociado.rolLabel = esAdmin ? "Coordinador (Admin)" : "Coordinador";
          uAsociado.aliases = [nombre.toLowerCase(), idExistente];
        }

        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/coordinadores.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "update",
                id: idExistente,
                nombre, zona, cedula, esAdmin,
                operador, operadorRol
              })
            });
          } catch (err) {
            console.error("Error sincronizando coordinador:", err);
          }
        }
        registrarEventoBitacora(operador, operadorRol, `Modificó coordinador: ${nombre} (${zona})${esAdmin ? " [Permiso Admin]" : ""}`);
      } else {
        const nuevoId = "coord_" + Date.now();
        coordinadores[nuevoId] = {
          id: nuevoId,
          nombre,
          zona,
          cedula,
          esAdmin
        };

        usuarios.push({
          id: "u_" + nuevoId,
          username: nombre,
          aliases: [nombre.toLowerCase(), nuevoId],
          nombreCompleto: `${nombre} (Coordinador ${zona})`,
          rol: nuevoId,
          rolLabel: esAdmin ? "Coordinador (Admin)" : "Coordinador",
          esAdmin,
          cedula,
          password: claveInicial,
          intentosFallidos: 0,
          bloqueado: false,
          primerLogin: true
        });

        if (modoConectado) {
          try {
            const res = await fetch(`${API_BASE}/coordinadores.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "create",
                nombre, zona, cedula, esAdmin,
                operador, operadorRol
              })
            });
            const json = await res.json();
            if (json.success && json.data && json.data.id) {
              coordinadores[json.data.id] = { id: json.data.id, nombre, zona, cedula, esAdmin };
            }
          } catch (err) {
            console.error("Error creando coordinador en servidor:", err);
          }
        }
        registrarEventoBitacora(operador, operadorRol, `Agregó nuevo coordinador: ${nombre} (${zona})${esAdmin ? " [Permiso Admin]" : ""}`);
      }

      guardarCoordinadoresDB();
      guardarUsuarios();
      actualizarSelectoresCoordinadores();
      cerrarModalCoordinador();
      actualizarTodoElSistema();
    }

    async function eliminarCoordinador(coordId) {
      if (!tieneControlTotal()) return;
      const lista = Object.keys(coordinadores);
      if (lista.length <= 1) {
        alert("Debe existir al menos un coordinador activo en el sistema.");
        return;
      }
      const c = coordinadores[coordId];
      if (!c) return;
      const afiliadosDeCoord = datos.filter(d => d.coordId === coordId).length;
      const msg = afiliadosDeCoord > 0
        ? `¿Desea eliminar al coordinador "${c.nombre}"? Tiene ${afiliadosDeCoord} afiliado(s) que pasarán al primer coordinador disponible.`
        : `¿Seguro que desea eliminar al coordinador "${c.nombre}" (${c.zona})?`;

      if (confirm(msg)) {
        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/coordinadores.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "delete",
                id: coordId,
                operador: usuarioAutenticado ? usuarioAutenticado.username : "Admin",
                operadorRol: usuarioAutenticado ? usuarioAutenticado.rolLabel : "Administrador"
              })
            });
          } catch (err) {
            console.error("Error eliminando coordinador en servidor:", err);
          }
        }
        delete coordinadores[coordId];
        const coordRestante = Object.keys(coordinadores)[0];
        if (afiliadosDeCoord > 0 && coordRestante) {
          datos = datos.map(d => d.coordId === coordId ? { ...d, coordId: coordRestante } : d);
          localStorage.setItem("padron_partido_db", JSON.stringify(datos));
        }
        usuarios = usuarios.filter(u => u.rol !== coordId);
        guardarCoordinadoresDB();
        guardarUsuarios();
        actualizarSelectoresCoordinadores();
        registrarEventoBitacora(usuarioAutenticado.username, usuarioAutenticado.rolLabel, `Eliminó coordinador: ${c.nombre}`);
        actualizarTodoElSistema();
      }
    }

    // =========================================================================
    // CONFIGURACIÓN DE INTERFAZ Y PERMISOS POR ROL
    // =========================================================================
    function configurarInterfazPorRol() {
      if (!usuarioAutenticado) return;

      actualizarSelectoresCoordinadores();

      document.getElementById("headerUserName").innerText = usuarioAutenticado.username;
      document.getElementById("headerUserRoleBadge").innerText = usuarioAutenticado.rolLabel;
      document.getElementById("headerUserMeta").innerText = `Cédula: ${usuarioAutenticado.cedula}`;
      document.getElementById("lblUltimoAcceso").innerHTML = `<i class="fa-regular fa-clock mr-1"></i>Ingreso: ${usuarioAutenticado.ultimoAcceso || "Ahora"}`;

      const badge = document.getElementById("roleBadge");
      const lblNombre = document.getElementById("roleUserName");
      const lblDesc = document.getElementById("roleUserDesc");
      const wrapFiltro = document.getElementById("wrapperFiltroCoord");
      const selectCoord = document.getElementById("formCoordinador");
      const msgBloqueo = document.getElementById("msgCoordBloqueado");
      const btnNuevoTop = document.getElementById("btnTopNuevoAfiliado");
      const btnNuevoCoordTop = document.getElementById("btnTopNuevoCoordinador");
      const btnDashCoord = document.getElementById("btnDashNuevoCoordinador");
      const btnPadronNuevo = document.getElementById("btnPadronNuevo");
      const btnPadronNuevoCoord = document.getElementById("btnPadronNuevoCoord");
      const panelAdminUsers = document.getElementById("panelAdminUsuarios");
      const panelGestionCoords = document.getElementById("panelGestionCoordinadores");
      const btnLimpiarBit = document.getElementById("btnLimpiarBitacora");
      const navDashboard = document.getElementById("navDashboard");
      const navComparador = document.getElementById("navComparador");
      const navSeguridad = document.getElementById("navSeguridad");
      const navAsistencia = document.getElementById("navAsistencia");
      const navMisionVision = document.getElementById("navMisionVision");
      const panelExportaciones = document.getElementById("panelExportacionesEjecutivas");
      const btnVerMiForm = document.getElementById("btnPadronVerMiFormulario");

      if (tieneControlTotal()) {
        if (rolActual === "campana") {
          badge.innerText = "Rol: Coordinador de Campaña (Control Total)";
        } else if (rolActual === "directora") {
          badge.innerText = "Rol: Directora Ejecutiva (Control Total)";
        } else if (rolActual === "admin") {
          badge.innerText = "Rol: Administrador Total";
        } else {
          badge.innerText = "Rol: Coordinador con Permiso de Administrador";
        }
        lblNombre.innerText = usuarioAutenticado.nombreCompleto;
        lblDesc.innerText = "Acceso total al sistema: Dashboard, padrón general, pase de lista por reuniones, agregar/modificar coordinadores, formularios y reportes.";
        wrapFiltro.classList.remove("hidden");
        selectCoord.disabled = false;
        msgBloqueo.classList.add("hidden");
        btnNuevoTop.classList.remove("hidden");
        btnNuevoCoordTop.classList.remove("hidden");
        btnDashCoord.classList.remove("hidden");
        btnPadronNuevo.classList.remove("hidden");
        btnPadronNuevoCoord.classList.remove("hidden");
        btnVerMiForm.classList.add("hidden");
        panelAdminUsers.classList.remove("hidden");
        panelGestionCoords.classList.remove("hidden");
        btnLimpiarBit.classList.remove("hidden");
        panelExportaciones.classList.remove("hidden");
        navDashboard.classList.remove("hidden");
        navComparador.classList.remove("hidden");
        navSeguridad.classList.remove("hidden");
        navAsistencia.classList.remove("hidden");
        navMisionVision.classList.remove("hidden");
        document.getElementById("filtroCoordinador").value = "todos";
        cambiarVista("dashboard");
      } else {
        const c = coordinadores[rolActual] || { nombre: usuarioAutenticado.username, zona: "Zona Asignada" };
        badge.innerText = "Rol: Coordinador Territorial";
        lblNombre.innerText = `${c.nombre} (${c.zona})`;
        lblDesc.innerText = "Puede agregar sus militantes nuevos y consultar cuántos y cuáles militantes tiene registrados en su formulario.";
        wrapFiltro.classList.add("hidden");
        selectCoord.value = rolActual;
        selectCoord.disabled = true;
        msgBloqueo.classList.remove("hidden");
        btnNuevoTop.classList.remove("hidden");
        btnNuevoCoordTop.classList.add("hidden");
        btnDashCoord.classList.add("hidden");
        btnPadronNuevo.classList.remove("hidden");
        btnPadronNuevoCoord.classList.add("hidden");
        btnVerMiForm.classList.remove("hidden");
        panelAdminUsers.classList.add("hidden");
        panelGestionCoords.classList.add("hidden");
        btnLimpiarBit.classList.add("hidden");
        panelExportaciones.classList.add("hidden");
        navDashboard.classList.add("hidden");
        navComparador.classList.add("hidden");
        navSeguridad.classList.add("hidden");
        navAsistencia.classList.add("hidden");
        navMisionVision.classList.add("hidden");
        cambiarVista("padron");
      }

      actualizarTodoElSistema();
    }

    function cambiarVista(vista) {
      if ((vista === "asistencia" || vista === "comparador" || vista === "seguridad" || vista === "dashboard" || vista === "misionVision") && !tieneControlTotal()) {
        return;
      }
      const vistas = {
        dashboard: document.getElementById("vistaDashboard"),
        padron: document.getElementById("vistaPadron"),
        comparador: document.getElementById("vistaComparador"),
        seguridad: document.getElementById("vistaSeguridad"),
        asistencia: document.getElementById("vistaAsistencia"),
        misionVision: document.getElementById("vistaMisionVision")
      };
      const botones = {
        dashboard: document.getElementById("navDashboard"),
        padron: document.getElementById("navPadron"),
        comparador: document.getElementById("navComparador"),
        seguridad: document.getElementById("navSeguridad"),
        asistencia: document.getElementById("navAsistencia"),
        misionVision: document.getElementById("navMisionVision")
      };

      Object.keys(vistas).forEach(k => {
        if (k === vista) {
          vistas[k].classList.remove("hidden");
          botones[k].className = "btn-modern !py-2.5 !px-5 !text-xs bg-amber-400 text-indigo-950";
        } else {
          vistas[k].classList.add("hidden");
          botones[k].className = "btn-modern !py-2.5 !px-5 !text-xs bg-indigo-950/70 text-indigo-100 hover:bg-indigo-800";
        }
      });

      if (vista === "dashboard") {
        actualizarGraficos();
      } else if (vista === "comparador") {
        actualizarComparador();
      } else if (vista === "seguridad") {
        renderSeguridadYBitacora();
      } else if (vista === "asistencia") {
        cargarSelectReuniones();
        renderPaseDeLista();
      }
    }

    function calcularEstadisticasCoordinadores() {
      const esSoloCoord = !tieneControlTotal() && rolActual && rolActual.startsWith("coord_");
      const coordsVisibles = esSoloCoord
        ? (coordinadores[rolActual] ? [coordinadores[rolActual]] : [])
        : Object.values(coordinadores);
      const datosVisibles = esSoloCoord
        ? datos.filter(d => d.coordId === rolActual)
        : datos;

      const totalGeneral = datosVisibles.length;
      const lista = coordsVisibles.map(c => {
        const cantidad = datosVisibles.filter(d => d.coordId === c.id).length;
        const porcentaje = totalGeneral > 0 ? Number(((cantidad / totalGeneral) * 100).toFixed(1)) : 0;
        return {
          ...c,
          cantidad,
          porcentaje
        };
      });

      const ordenados = [...lista].sort((a, b) => b.cantidad - a.cantidad);
      const maxCant = ordenados.length > 0 ? ordenados[0].cantidad : 0;
      const minCant = ordenados.length > 0 ? ordenados[ordenados.length - 1].cantidad : 0;

      lista.forEach(item => {
        if (item.cantidad === maxCant && maxCant > 0) {
          item.nivel = "Mayor cantidad de afiliados";
          item.semaforo = "verde";
          item.colorHex = "#10b981";
          item.badgeClass = "bg-emerald-100 text-emerald-800 border-emerald-300";
        } else if (item.cantidad === minCant) {
          item.nivel = "Menor cantidad de afiliados";
          item.semaforo = "rojo";
          item.colorHex = "#f43f5e";
          item.badgeClass = "bg-rose-100 text-rose-800 border-rose-300";
        } else {
          item.nivel = "Rendimiento medio";
          item.semaforo = "amarillo";
          item.colorHex = "#f59e0b";
          item.badgeClass = "bg-amber-100 text-amber-800 border-amber-300";
        }
      });

      const promedio = lista.length > 0 ? (totalGeneral / lista.length).toFixed(1) : "0.0";

      return {
        totalGeneral,
        totalCoordinadores: lista.length,
        promedio,
        lista,
        ordenados,
        mayor: ordenados[0] || null,
        menor: ordenados[ordenados.length - 1] || null
      };
    }

    function renderDashboardKPIsYSemaforo() {
      const stats = calcularEstadisticasCoordinadores();

      document.getElementById("kpiTotalAfiliados").innerText = stats.totalGeneral;
      document.getElementById("kpiTotalCoordinadores").innerText = stats.totalCoordinadores;
      document.getElementById("kpiPromedioCoord").innerText = stats.promedio;

      if (stats.mayor) {
        document.getElementById("kpiMayorNombre").innerText = stats.mayor.nombre;
        document.getElementById("kpiMayorDetalle").innerText = `${stats.mayor.cantidad} afiliados (${stats.mayor.porcentaje}%)`;
      }
      if (stats.menor) {
        document.getElementById("kpiMenorNombre").innerText = stats.menor.nombre;
        document.getElementById("kpiMenorDetalle").innerText = `${stats.menor.cantidad} afiliados (${stats.menor.porcentaje}%)`;
      }

      const contenedor = document.getElementById("cardsCoordinadores");
      contenedor.innerHTML = "";

      stats.ordenados.forEach((c, idx) => {
        const borderSemaforo = c.semaforo === "verde"
          ? "border-emerald-500"
          : c.semaforo === "amarillo"
          ? "border-amber-500"
          : "border-rose-500";

        const bgBarra = c.semaforo === "verde"
          ? "bg-emerald-500"
          : c.semaforo === "amarillo"
          ? "bg-amber-500"
          : "bg-rose-500";

        const botonesCoord = tieneControlTotal() ? `
          <div class="flex justify-between items-center gap-1.5 pt-2 border-t border-slate-200/70">
            <span class="text-[11px] font-bold text-indigo-700"><i class="fa-solid fa-clipboard-list mr-1"></i>Clic para ver formulario</span>
            <div class="flex gap-1.5">
              <button onclick="event.stopPropagation(); editarCoordinador('${c.id}')" class="text-xs font-bold px-2.5 py-1 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200 transition" title="Editar Coordinador">
                <i class="fa-solid fa-pen"></i> Editar
              </button>
              <button onclick="event.stopPropagation(); eliminarCoordinador('${c.id}')" class="text-xs font-bold px-2.5 py-1 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 transition" title="Eliminar Coordinador">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>
          </div>
        ` : `<div class="pt-2 border-t border-slate-200/70 text-[11px] font-bold text-indigo-700"><i class="fa-solid fa-clipboard-list mr-1"></i>Clic para ver miembros</div>`;

        contenedor.innerHTML += `
          <div onclick="verFormularioCoordinador('${c.id}')" class="surface-sub rounded-2xl p-5 bg-slate-50 border-l-8 ${borderSemaforo} shadow-sm flex flex-col justify-between gap-3 cursor-pointer hover:shadow-md hover:ring-2 hover:ring-indigo-400 transition">
            <div class="flex justify-between items-start">
              <div>
                <span class="text-[11px] font-extrabold uppercase px-2.5 py-0.5 rounded-full border ${c.badgeClass}">
                  #${idx + 1} • ${c.nivel}
                </span>
                <h4 class="text-lg font-extrabold text-slate-900 mt-2">${c.nombre}</h4>
                <p class="text-xs text-slate-500 font-medium"><i class="fa-solid fa-location-dot mr-1"></i>${c.zona}</p>
              </div>
              <div class="text-right">
                <span class="text-2xl font-black text-slate-900">${c.cantidad}</span>
                <span class="block text-[11px] font-bold text-slate-500">Afiliados</span>
              </div>
            </div>
            <div>
              <div class="flex justify-between text-xs font-extrabold mb-1">
                <span>Participación en el Padrón</span>
                <span>${c.porcentaje}%</span>
              </div>
              <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full ${bgBarra} rounded-full transition-all duration-500" style="width: ${c.porcentaje}%"></div>
              </div>
            </div>
            ${botonesCoord}
          </div>
        `;
      });
    }

    function actualizarGraficos() {
      const stats = calcularEstadisticasCoordinadores();
      const esTemaOscuro = document.body.classList.contains("theme-negro");
      const textColor = esTemaOscuro ? "#f1f5f9" : "#334155";
      const gridColor = esTemaOscuro ? "rgba(148, 163, 184, 0.15)" : "rgba(148, 163, 184, 0.25)";

      const paletaCircular = ["#4f46e5", "#0ea5e9", "#8b5cf6", "#10b981", "#f59e0b", "#ec4899", "#14b8a6", "#f43f5e"];
      const labels = stats.lista.map(c => `${c.nombre} (${c.porcentaje}%)`);
      const cantidades = stats.lista.map(c => c.cantidad);
      const porcentajes = stats.lista.map(c => c.porcentaje);
      const coloresSemaforo = stats.lista.map(c => c.colorHex);

      const divLeyenda = document.getElementById("leyendaPorcentajesPie");
      divLeyenda.innerHTML = stats.lista.map(c => `
        <div class="p-2 rounded-xl surface-sub bg-slate-50 border border-slate-200/60">
          <span class="inline-block w-3 h-3 rounded-full mr-1" style="background:${c.colorHex}"></span>
          <span class="font-bold text-slate-800 block truncate">${c.nombre}</span>
          <span class="text-sm font-black" style="color:${c.colorHex}">${c.porcentaje}% (${c.cantidad})</span>
        </div>
      `).join("");

      const ctxPie = document.getElementById("chartPieCoordinadores").getContext("2d");
      if (chartPieInstance) chartPieInstance.destroy();
      chartPieInstance = new Chart(ctxPie, {
        type: "pie",
        data: {
          labels,
          datasets: [{
            data: porcentajes,
            backgroundColor: stats.lista.map((_, i) => paletaCircular[i % paletaCircular.length]),
            borderColor: esTemaOscuro ? "#1e293b" : "#ffffff",
            borderWidth: 3,
            hoverOffset: 10
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "bottom",
              labels: { color: textColor, font: { weight: "bold", size: 11 } }
            },
            tooltip: {
              callbacks: {
                label: function(ctx) {
                  const item = stats.lista[ctx.dataIndex];
                  return ` ${item.nombre}: ${item.porcentaje}% (${item.cantidad} afiliados)`;
                }
              }
            }
          }
        }
      });

      const ctxBar = document.getElementById("chartBarCoordinadores").getContext("2d");
      if (chartBarInstance) chartBarInstance.destroy();
      chartBarInstance = new Chart(ctxBar, {
        type: "bar",
        data: {
          labels: stats.lista.map(c => `${c.nombre} - ${c.zona}`),
          datasets: [{
            label: "Cantidad de Afiliados",
            data: cantidades,
            backgroundColor: coloresSemaforo,
            borderRadius: 10,
            barThickness: 44
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1, color: textColor, font: { weight: "bold" } },
              grid: { color: gridColor }
            },
            x: {
              ticks: { color: textColor, font: { weight: "bold" } },
              grid: { display: false }
            }
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function(ctx) {
                  const item = stats.lista[ctx.dataIndex];
                  return ` ${item.cantidad} afiliados (${item.porcentaje}% del total) - ${item.nivel}`;
                }
              }
            }
          }
        }
      });

      const mesesOrden = ["Ene", "Feb", "Mar", "Abr", "May", "Jun"];
      const ctxTrend = document.getElementById("chartTrendMeses").getContext("2d");
      if (chartTrendInstance) chartTrendInstance.destroy();

      const datasetsTrend = stats.lista.map((c, i) => {
        const colorLinea = paletaCircular[i % paletaCircular.length];
        let acumulado = 0;
        const serieAcumulada = mesesOrden.map(m => {
          const delMes = datos.filter(d => d.coordId === c.id && d.mes === m).length;
          acumulado += delMes;
          return acumulado;
        });
        return {
          label: `${c.nombre} (${c.zona})`,
          data: serieAcumulada,
          borderColor: colorLinea,
          backgroundColor: colorLinea + "22",
          borderWidth: 3,
          tension: 0.35,
          fill: true,
          pointRadius: 5,
          pointHoverRadius: 7
        };
      });

      chartTrendInstance = new Chart(ctxTrend, {
        type: "line",
        data: {
          labels: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio"],
          datasets: datasetsTrend
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              ticks: { stepSize: 1, color: textColor, font: { weight: "bold" } },
              grid: { color: gridColor }
            },
            x: {
              ticks: { color: textColor, font: { weight: "bold" } },
              grid: { color: gridColor }
            }
          },
          plugins: {
            legend: {
              position: "top",
              labels: { color: textColor, font: { weight: "bold", size: 12 } }
            }
          }
        }
      });
    }

    function actualizarComparador() {
      const checks = Array.from(document.querySelectorAll(".chk-comparar:checked")).map(el => el.value);
      const tbody = document.getElementById("tablaComparativaBody");
      tbody.innerHTML = "";

      if (checks.length < 2) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-6 text-center text-amber-600 font-bold">Seleccione al menos dos (2) coordinadores arriba para realizar una comparación.</td></tr>`;
        document.getElementById("compLiderNombre").innerText = "Seleccione al menos 2 coordinadores";
        document.getElementById("compLiderPorcentaje").innerText = "---";
        return;
      }

      const stats = calcularEstadisticasCoordinadores();
      const seleccionados = stats.ordenados.filter(c => checks.includes(c.id));
      const totalGrupo = seleccionados.reduce((acc, c) => acc + c.cantidad, 0);

      const lider = seleccionados[0];
      document.getElementById("compLiderNombre").innerText = `${lider.nombre} (${lider.zona}) — ${lider.cantidad} Afiliados`;
      document.getElementById("compLiderPorcentaje").innerText = `${lider.porcentaje}%`;

      seleccionados.forEach((c, idx) => {
        const pctGrupo = totalGrupo > 0 ? ((c.cantidad / totalGrupo) * 100).toFixed(1) : "0.0";
        const badgeSemaforo = c.semaforo === "verde"
          ? `<span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800"><i class="fa-solid fa-circle text-emerald-500 mr-1.5"></i>Verde: Mayor cantidad</span>`
          : c.semaforo === "amarillo"
          ? `<span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800"><i class="fa-solid fa-circle text-amber-500 mr-1.5"></i>Amarillo: Rendimiento medio</span>`
          : `<span class="px-3 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800"><i class="fa-solid fa-circle text-rose-500 mr-1.5"></i>Rojo: Menor cantidad</span>`;

        tbody.innerHTML += `
          <tr onclick="verFormularioCoordinador('${c.id}')" class="hover:bg-indigo-50/70 cursor-pointer transition">
            <td class="p-4 font-black text-indigo-600">#${idx + 1}</td>
            <td class="p-4">
              <p class="font-extrabold text-slate-900 underline decoration-indigo-300">${c.nombre}</p>
              <p class="text-xs text-slate-500">${c.zona}</p>
            </td>
            <td class="p-4 font-black text-lg text-slate-900">${c.cantidad} afiliados</td>
            <td class="p-4 font-bold text-indigo-600">${c.porcentaje}%</td>
            <td class="p-4 font-bold text-emerald-600">${pctGrupo}% del grupo</td>
            <td class="p-4">${badgeSemaforo}</td>
          </tr>
        `;
      });
    }

    function renderPadron() {
      const tbody = document.getElementById("tablaIntegrantes");
      const texto = (document.getElementById("inputBuscar").value || "").toLowerCase();
      const fCoord = document.getElementById("filtroCoordinador").value;
      tbody.innerHTML = "";

      let filtrados = datos.filter(item => {
        if (!tieneControlTotal() && rolActual && rolActual.startsWith("coord_") && item.coordId !== rolActual) return false;
        if (tieneControlTotal() && fCoord !== "todos" && item.coordId !== fCoord) return false;
        if (texto) {
          const matchNom = item.nombre.toLowerCase().includes(texto);
          const matchCed = item.cedula.toLowerCase().includes(texto);
          const matchCenDir = (item.centroDireccion || item.centroVotacion || item.recinto || "").toLowerCase().includes(texto);
          const matchMes = (item.mesaElectoral || "").toLowerCase().includes(texto);
          const matchFol = item.folio.toLowerCase().includes(texto);
          if (!matchNom && !matchCed && !matchCenDir && !matchMes && !matchFol) return false;
        }
        return true;
      });

      if (!tieneControlTotal() && rolActual && rolActual.startsWith("coord_")) {
        const totalPropios = datos.filter(d => d.coordId === rolActual).length;
        document.getElementById("lblConteo").innerText = `Mostrando ${filtrados.length} de ${totalPropios} miembros de su coordinación`;
      } else {
        document.getElementById("lblConteo").innerText = `Mostrando ${filtrados.length} afiliados de un total general de ${datos.length} registrados`;
      }

      if (filtrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="8" class="p-8 text-center text-slate-400 font-semibold">No se encontraron afiliados con los criterios seleccionados.</td></tr>`;
        return;
      }

      filtrados.forEach(item => {
        const c = coordinadores[item.coordId] || { nombre: "Sin Asignar", zona: "" };
        let botones = "";
        if (tieneControlTotal()) {
          botones = `
            <button onclick="imprimirConstancia(${item.id})" class="btn-action text-indigo-700 bg-indigo-100 hover:bg-indigo-200 mr-1.5" title="Emitir Constancia Oficial">
              <i class="fa-solid fa-print"></i> Constancia
            </button>
            <button onclick="editar(${item.id})" class="btn-action text-amber-800 bg-amber-100 hover:bg-amber-200 mr-1.5" title="Editar Afiliado">
              <i class="fa-solid fa-pen"></i> Editar
            </button>
            <button onclick="eliminar(${item.id})" class="btn-action text-rose-700 bg-rose-100 hover:bg-rose-200" title="Eliminar del Padrón">
              <i class="fa-solid fa-trash"></i> Eliminar
            </button>
          `;
        } else {
          botones = `
            <button onclick="verFormularioCoordinador('${item.coordId}')" class="btn-action text-indigo-700 bg-indigo-100 hover:bg-indigo-200" title="Ver en Formulario">
              <i class="fa-solid fa-clipboard-list"></i> Ver Formulario
            </button>
          `;
        }

        tbody.innerHTML += `
          <tr class="hover:bg-slate-50 transition">
            <td class="p-4 font-mono font-bold text-slate-900">
              ${item.folio}
              <div class="text-xs text-slate-500 font-normal">${item.cedula}</div>
            </td>
            <td class="p-4 font-bold text-slate-900">${item.nombre}</td>
            <td class="p-4">${item.telefono}</td>
            <td class="p-4">
              <div class="font-bold text-slate-800">${item.centroDireccion || item.centroVotacion || item.recinto || "---"}</div>
            </td>
            <td class="p-4 font-semibold text-indigo-700">${item.mesaElectoral || "---"}</td>
            <td class="p-4 text-xs font-semibold text-slate-500">${item.fecha || "Jun 2026"}</td>
            <td class="p-4">
              <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full font-extrabold text-xs">
                ${c.nombre}
              </span>
            </td>
            <td class="p-4 text-right whitespace-nowrap">${botones}</td>
          </tr>
        `;
      });
    }

    function abrirModalNuevo(coordPreseleccionado) {
      document.getElementById("formId").value = "";
      document.getElementById("formCedula").value = "";
      document.getElementById("formNombre").value = "";
      document.getElementById("formTelefono").value = "";
      document.getElementById("formCentroDireccion").value = "";
      document.getElementById("formMesaElectoral").value = "";
      document.getElementById("formMes").value = "Jun";
      document.getElementById("modalTitulo").innerHTML = `<i class="fa-solid fa-user-plus text-amber-400"></i> Inscribir Miembros (Militante)`;
      if (!tieneControlTotal() && rolActual && rolActual.startsWith("coord_")) {
        document.getElementById("formCoordinador").value = rolActual;
      } else if (coordPreseleccionado && coordinadores[coordPreseleccionado]) {
        document.getElementById("formCoordinador").value = coordPreseleccionado;
      }
      document.getElementById("modalForm").classList.remove("hidden");
    }

    function cerrarModal() {
      document.getElementById("modalForm").classList.add("hidden");
    }

    async function guardarIntegrante(e) {
      e.preventDefault();
      const id = document.getElementById("formId").value;
      const cedula = document.getElementById("formCedula").value.trim();
      const nombre = document.getElementById("formNombre").value.trim();
      const telefono = document.getElementById("formTelefono").value.trim();
      const centroDireccion = document.getElementById("formCentroDireccion").value.trim();
      const mesaElectoral = document.getElementById("formMesaElectoral").value.trim();
      const coordId = (!tieneControlTotal() && rolActual && rolActual.startsWith("coord_"))
        ? rolActual
        : document.getElementById("formCoordinador").value;
      const mes = document.getElementById("formMes").value;
      const operador = usuarioAutenticado ? usuarioAutenticado.username : "Usuario";
      const operadorRol = usuarioAutenticado ? usuarioAutenticado.rolLabel : "Coordinador";

      if (id) {
        const item = datos.find(d => d.id == id);
        if (item) {
          item.cedula = cedula;
          item.nombre = nombre;
          item.telefono = telefono;
          item.centroDireccion = centroDireccion;
          item.mesaElectoral = mesaElectoral;
          item.coordId = coordId;
          item.mes = mes;
        }
        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/afiliados.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "update",
                id: parseInt(id),
                cedula, nombre, telefono, centroDireccion, mesaElectoral, coordId, mes,
                operador, operadorRol
              })
            });
          } catch (err) {
            console.error("Error sincronizando afiliado con servidor:", err);
          }
        }
      } else {
        const hoy = new Date().toLocaleDateString("es-DO");
        let nuevoAfiliado = {
          id: Date.now(),
          folio: "PAD-2026-" + Math.floor(100 + Math.random() * 900),
          cedula,
          nombre,
          telefono,
          centroDireccion,
          mesaElectoral,
          coordId,
          mes,
          fecha: hoy
        };

        if (modoConectado) {
          try {
            const res = await fetch(`${API_BASE}/afiliados.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "create",
                cedula, nombre, telefono, centroDireccion, mesaElectoral, coordId, mes,
                operador, operadorRol
              })
            });
            const json = await res.json();
            if (json.success && json.data) {
              nuevoAfiliado = json.data;
            }
          } catch (err) {
            console.error("Error guardando afiliado en servidor:", err);
          }
        }
        datos.push(nuevoAfiliado);
      }
      localStorage.setItem("padron_partido_db", JSON.stringify(datos));
      cerrarModal();
      actualizarTodoElSistema();
      if (!document.getElementById("modalFormularioMiembros").classList.contains("hidden")) {
        refrescarFormularioMiembrosAbierto();
      }
    }

    function editar(id) {
      const item = datos.find(d => d.id === id);
      if (!item) return;
      if (!tieneControlTotal() && item.coordId !== rolActual) return;
      document.getElementById("formId").value = item.id;
      document.getElementById("formCedula").value = item.cedula;
      document.getElementById("formNombre").value = item.nombre;
      document.getElementById("formTelefono").value = item.telefono;
      document.getElementById("formCentroDireccion").value = item.centroDireccion || item.centroVotacion || item.recinto || "";
      document.getElementById("formMesaElectoral").value = item.mesaElectoral || "";
      document.getElementById("formCoordinador").value = item.coordId;
      document.getElementById("formMes").value = item.mes || "Jun";
      document.getElementById("modalTitulo").innerHTML = `<i class="fa-solid fa-user-pen text-amber-400"></i> Modificar Datos de Persona`;
      document.getElementById("modalForm").classList.remove("hidden");
    }

    async function eliminar(id) {
      if (confirm("¿Está seguro de que desea eliminar este afiliado del padrón? Los gráficos se actualizarán inmediatamente.")) {
        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/afiliados.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "delete",
                id: parseInt(id),
                operador: usuarioAutenticado ? usuarioAutenticado.username : "Usuario",
                operadorRol: usuarioAutenticado ? usuarioAutenticado.rolLabel : "Administrador"
              })
            });
          } catch (err) {
            console.error("Error al eliminar en servidor:", err);
          }
        }
        datos = datos.filter(d => d.id !== id);
        localStorage.setItem("padron_partido_db", JSON.stringify(datos));
        actualizarTodoElSistema();
      }
    }

    function actualizarTodoElSistema() {
      renderDashboardKPIsYSemaforo();
      renderPadron();
      actualizarGraficos();
      actualizarComparador();
      renderSeguridadYBitacora();
    }

    function renderSeguridadYBitacora() {
      // Tabla de Coordinadores en la pestaña Seguridad/Coordinadores
      const tbodyCoords = document.getElementById("tablaCoordinadoresGestion");
      if (tbodyCoords) {
        tbodyCoords.innerHTML = Object.values(coordinadores).map(c => {
          const cant = datos.filter(d => d.coordId === c.id).length;
          const badgePermiso = c.esAdmin
            ? `<span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-400 text-indigo-950">Admin</span>`
            : `<span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-slate-200 text-slate-700">Estándar</span>`;
          return `
            <tr onclick="verFormularioCoordinador('${c.id}')" class="hover:bg-indigo-50/70 cursor-pointer transition">
              <td class="p-3.5 font-extrabold text-indigo-900 underline decoration-indigo-300">${c.nombre}${badgePermiso}</td>
              <td class="p-3.5">${c.zona}</td>
              <td class="p-3.5 font-mono text-xs">${c.cedula || "---"}</td>
              <td class="p-3.5 font-bold text-indigo-600">${cant} afiliados</td>
              <td class="p-3.5 text-right whitespace-nowrap">
                <button onclick="event.stopPropagation(); verFormularioCoordinador('${c.id}')" class="btn-action bg-indigo-100 hover:bg-indigo-200 text-indigo-800 mr-1" title="Ver Formulario de Miembros">
                  <i class="fa-solid fa-clipboard-list"></i> Formulario
                </button>
                <button onclick="event.stopPropagation(); editarCoordinador('${c.id}')" class="btn-action bg-amber-100 hover:bg-amber-200 text-amber-800 mr-1">
                  <i class="fa-solid fa-pen"></i> Editar
                </button>
                <button onclick="event.stopPropagation(); eliminarCoordinador('${c.id}')" class="btn-action bg-rose-100 hover:bg-rose-200 text-rose-700">
                  <i class="fa-solid fa-trash"></i> Eliminar
                </button>
              </td>
            </tr>
          `;
        }).join("");
      }

      const tbodyUsers = document.getElementById("tablaUsuariosSeguridad");
      if (tbodyUsers) {
        tbodyUsers.innerHTML = usuarios.map(u => {
          const claveInicial = obtenerUltimos4Cedula(u.cedula);
          const estadoBadge = u.bloqueado
            ? `<span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">Bloqueado (${u.intentosFallidos}/3)</span>`
            : `<span class="px-2.5 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Activo (${u.intentosFallidos}/3 fallos)</span>`;
          return `
            <tr onclick="verFormularioUsuario('${u.id}')" class="hover:bg-indigo-50/70 cursor-pointer transition">
              <td class="p-3.5 font-extrabold text-indigo-900 underline decoration-indigo-300">${u.username}</td>
              <td class="p-3.5">${u.nombreCompleto}</td>
              <td class="p-3.5 font-mono text-xs">${u.cedula}</td>
              <td class="p-3.5 font-mono font-bold text-indigo-600">${claveInicial}</td>
              <td class="p-3.5">${estadoBadge}</td>
              <td class="p-3.5 text-right whitespace-nowrap">
                <button onclick="event.stopPropagation(); verFormularioUsuario('${u.id}')" class="btn-action bg-sky-100 hover:bg-sky-200 text-sky-800 mr-1" title="Ver Personas Agregadas en Formulario">
                  <i class="fa-solid fa-clipboard-list"></i> Ver Formulario
                </button>
                <button onclick="event.stopPropagation(); adminRestablecerPassword('${u.id}')" class="btn-action bg-indigo-600 hover:bg-indigo-700 text-white">
                  <i class="fa-solid fa-rotate-left"></i> Restablecer a ${claveInicial}
                </button>
              </td>
            </tr>
          `;
        }).join("");
      }

      const tbodyBit = document.getElementById("tablaBitacoraAccesos");
      if (tbodyBit) {
        if (bitacoraAccesos.length === 0) {
          tbodyBit.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-slate-400">Sin registros en la bitácora.</td></tr>`;
        } else {
          tbodyBit.innerHTML = bitacoraAccesos.map(b => {
            const esExito = b.estado.includes("Exitoso");
            const colorClase = esExito ? "text-emerald-700 bg-emerald-50" : "text-rose-700 bg-rose-50";
            return `
              <tr class="hover:bg-slate-50">
                <td class="p-3 font-mono font-semibold">${b.fechaHora}</td>
                <td class="p-3 font-bold text-slate-900">${b.username}</td>
                <td class="p-3">${b.rolLabel}</td>
                <td class="p-3"><span class="px-2.5 py-1 rounded-lg font-bold ${colorClase}">${b.estado}</span></td>
              </tr>
            `;
          }).join("");
        }
      }
    }

    async function limpiarBitacora() {
      if (!tieneControlTotal()) return;
      if (confirm("¿Desea limpiar el historial de accesos?")) {
        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/bitacora.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "clear",
                operador: usuarioAutenticado.username,
                operadorRol: usuarioAutenticado.rolLabel
              })
            });
          } catch (err) {
            console.error("Error al limpiar bitácora en servidor:", err);
          }
        }
        bitacoraAccesos = [];
        localStorage.setItem("padron_bitacora_v2", JSON.stringify(bitacoraAccesos));
        renderSeguridadYBitacora();
      }
    }

    function exportarExcelCompleto() {
      const stats = calcularEstadisticasCoordinadores();

      const resumenData = stats.ordenados.map((c, index) => ({
        "Posición": index + 1,
        "Coordinador": c.nombre,
        "Zona Territorial": c.zona,
        "Afiliados Registrados": c.cantidad,
        "Porcentaje (%)": c.porcentaje + "%",
        "Nivel de Rendimiento": c.nivel
      }));
      resumenData.push({
        "Posición": "TOTAL",
        "Coordinador": "PADRÓN GENERAL",
        "Zona Territorial": "Todas las Zonas",
        "Afiliados Registrados": stats.totalGeneral,
        "Porcentaje (%)": "100%",
        "Nivel de Rendimiento": `Promedio: ${stats.promedio} por coord.`
      });

      const esSoloCoord = !tieneControlTotal() && rolActual && rolActual.startsWith("coord_");
      const datosExportar = esSoloCoord ? datos.filter(d => d.coordId === rolActual) : datos;

      const padronData = datosExportar.map(d => {
        const c = coordinadores[d.coordId] || { nombre: "Sin Asignar", zona: "" };
        return {
          "Folio": d.folio,
          "Cédula": d.cedula,
          "Nombre Completo": d.nombre,
          "Teléfono": d.telefono,
          "Centro de Votación / Dirección": d.centroDireccion || d.centroVotacion || d.recinto || "",
          "Mesa Electoral": d.mesaElectoral || "",
          "Coordinador Asignado": c.nombre,
          "Zona": c.zona,
          "Fecha Registro": d.fecha || "2026"
        };
      });

      if (typeof XLSX !== "undefined") {
        const wb = XLSX.utils.book_new();
        const wsResumen = XLSX.utils.json_to_sheet(resumenData);
        const wsPadron = XLSX.utils.json_to_sheet(padronData);
        XLSX.utils.book_append_sheet(wb, wsResumen, "Estadisticas_Coordinadores");
        XLSX.utils.book_append_sheet(wb, wsPadron, "Padron_Afiliados");
        XLSX.writeFile(wb, "Reporte_Ejecutivo_Padron_Electoral.xlsx");
      } else {
        let csv = "\uFEFFFolio,Cedula,Nombre,Telefono,Centro de Votacion / Direccion,Mesa Electoral,Coordinador\n";
        datosExportar.forEach(d => {
          const c = coordinadores[d.coordId] ? coordinadores[d.coordId].nombre : "";
          const cenDir = d.centroDireccion || d.centroVotacion || d.recinto || "";
          csv += `"${d.folio}","${d.cedula}","${d.nombre}","${d.telefono}","${cenDir}","${d.mesaElectoral || ""}","${c}"\n`;
        });
        const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
        const a = document.createElement("a");
        a.href = URL.createObjectURL(blob);
        a.download = "Reporte_Padron_Electoral.csv";
        a.click();
      }
    }

    function exportarReportePDF() {
      cambiarVista("dashboard");
      const elemento = document.getElementById("vistaDashboard");
      if (typeof html2pdf !== "undefined") {
        const opt = {
          margin: 0.35,
          filename: "Dashboard_Estadistico_Padron_Electoral.pdf",
          image: { type: "jpeg", quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true },
          jsPDF: { unit: "in", format: "letter", orientation: "landscape" }
        };
        html2pdf().set(opt).from(elemento).save();
      } else {
        imprimirReporteEstadistico();
      }
    }

    function imprimirConstancia(id) {
      const item = datos.find(d => d.id === id);
      if (!item) return;
      const c = coordinadores[item.coordId] || { nombre: "Coordinador Territorial", zona: "" };
      const printArea = document.getElementById("printArea");

      printArea.innerHTML = `
        <div class="max-w-2xl mx-auto p-8 border-4 border-double border-indigo-900 rounded-2xl bg-white text-slate-800 my-6">
          <div class="text-center border-b-2 border-indigo-900 pb-4 mb-6">
            <h2 class="text-2xl font-black text-indigo-950 uppercase">PARTIDO POLÍTICO INSTITUCIONAL</h2>
            <h3 class="text-xs font-bold text-slate-600 mt-1">DIRECCIÓN NACIONAL DE ORGANIZACIÓN Y PADRÓN ELECTORAL</h3>
          </div>
          <div class="text-center mb-6">
            <span class="bg-indigo-50 border border-indigo-200 text-indigo-900 font-bold text-xs px-4 py-1.5 rounded-full uppercase">
              CONSTANCIA OFICIAL DE INSCRIPCIÓN EN EL PADRÓN
            </span>
            <p class="text-xs text-slate-500 mt-2">Folio Oficial: <strong class="text-slate-900">${item.folio}</strong></p>
          </div>
          <div class="text-sm leading-relaxed space-y-4 mb-8 text-justify">
            <p>Por medio del presente documento oficial, la <strong>Dirección Ejecutiva del Padrón Electoral</strong> certifica que el(la) ciudadano(a):</p>
            <div class="p-4 bg-slate-50 border border-slate-300 rounded-xl text-center">
              <p class="text-lg font-black text-indigo-950 uppercase">${item.nombre}</p>
              <p class="text-xs text-slate-600 mt-1">Cédula: <strong>${item.cedula}</strong> | Teléfono: <strong>${item.telefono}</strong></p>
              <p class="text-xs text-slate-600 mt-1">Centro de Votación / Dirección: <strong>${item.centroDireccion || item.centroVotacion || item.recinto || "---"}</strong> | Mesa Electoral: <strong>${item.mesaElectoral || "---"}</strong></p>
            </div>
            <p>Se encuentra debidamente registrado(a) y validado(a) como afiliado(a) activo(a) adscrito(a) a la coordinación territorial de: <strong>${c.nombre} (${c.zona})</strong>.</p>
          </div>
          <div class="grid grid-cols-2 gap-8 text-center pt-8 border-t border-slate-300 text-xs mt-10">
            <div>
              <p class="font-serif italic font-bold text-base text-indigo-900">María Castillo</p>
              <p class="font-bold border-t border-slate-400 mt-1 pt-1">Licda. María Castillo</p>
              <p class="text-[11px] text-slate-500">Directora Ejecutiva de Padrón</p>
            </div>
            <div>
              <p class="font-serif italic font-bold text-base text-indigo-900">${c.nombre}</p>
              <p class="font-bold border-t border-slate-400 mt-1 pt-1">${c.nombre}</p>
              <p class="text-[11px] text-slate-500">Coordinador Responsable (${c.zona})</p>
            </div>
          </div>
        </div>
      `;

      printArea.classList.remove("hidden");
      window.print();
      setTimeout(() => printArea.classList.add("hidden"), 500);
    }

    function imprimirReporteEstadistico() {
      const stats = calcularEstadisticasCoordinadores();
      const fechaHoy = new Date().toLocaleString("es-DO");
      const printArea = document.getElementById("printArea");

      const filasCoords = stats.ordenados.map((c, idx) => `
        <tr class="border-b border-slate-300">
          <td class="p-2.5 font-bold">#${idx + 1}</td>
          <td class="p-2.5 font-bold">${c.nombre}</td>
          <td class="p-2.5">${c.zona}</td>
          <td class="p-2.5 font-bold">${c.cantidad}</td>
          <td class="p-2.5 font-bold">${c.porcentaje}%</td>
          <td class="p-2.5">${c.nivel}</td>
        </tr>
      `).join("");

      printArea.innerHTML = `
        <div class="max-w-4xl mx-auto p-8 bg-white text-slate-900">
          <div class="flex justify-between items-center border-b-2 border-indigo-950 pb-4 mb-6">
            <div>
              <h1 class="text-xl font-black uppercase text-indigo-950">REPORTE EJECUTIVO DE ESTADÍSTICAS Y PADRÓN ELECTORAL</h1>
              <p class="text-xs text-slate-600">Emitido por: ${usuarioAutenticado ? usuarioAutenticado.nombreCompleto : "Sistema"} | Fecha: ${fechaHoy}</p>
            </div>
            <div class="text-right text-xs font-bold">
              <p>Total Afiliados: ${stats.totalGeneral}</p>
              <p>Promedio por Coord.: ${stats.promedio}</p>
            </div>
          </div>

          <div class="grid grid-cols-3 gap-4 mb-6 text-xs">
            <div class="p-3 border border-slate-300 rounded-xl">
              <p class="text-slate-500 font-bold uppercase">Mayor Rendimiento (Verde)</p>
              <p class="text-base font-black mt-1">${stats.mayor ? stats.mayor.nombre : "---"}</p>
              <p>${stats.mayor ? stats.mayor.cantidad + " afiliados (" + stats.mayor.porcentaje + "%)" : ""}</p>
            </div>
            <div class="p-3 border border-slate-300 rounded-xl">
              <p class="text-slate-500 font-bold uppercase">Menor Cantidad (Rojo)</p>
              <p class="text-base font-black mt-1">${stats.menor ? stats.menor.nombre : "---"}</p>
              <p>${stats.menor ? stats.menor.cantidad + " afiliados (" + stats.menor.porcentaje + "%)" : ""}</p>
            </div>
            <div class="p-3 border border-slate-300 rounded-xl">
              <p class="text-slate-500 font-bold uppercase">Coordinadores Activos</p>
              <p class="text-base font-black mt-1">${stats.totalCoordinadores} Coordinaciones</p>
              <p>Promedio: ${stats.promedio} afiliados/coord.</p>
            </div>
          </div>

          <h3 class="text-sm font-black uppercase mb-2">Desglose Porcentual por Coordinador</h3>
          <table class="w-full text-left text-xs border border-slate-300 mb-6">
            <thead class="bg-slate-100 border-b border-slate-300">
              <tr>
                <th class="p-2.5">Pos.</th>
                <th class="p-2.5">Coordinador</th>
                <th class="p-2.5">Zona</th>
                <th class="p-2.5">Afiliados</th>
                <th class="p-2.5">% Participación</th>
                <th class="p-2.5">Indicador de Rendimiento</th>
              </tr>
            </thead>
            <tbody>${filasCoords}</tbody>
          </table>
        </div>
      `;

      printArea.classList.remove("hidden");
      window.print();
      setTimeout(() => printArea.classList.add("hidden"), 500);
    }

    // =========================================================================
    // FORMULARIO OFICIAL DE MIEMBROS POR COORDINADOR / USUARIO (HOJAS DE 10)
    // =========================================================================
    let formularioContexto = {
      tipo: "coord",
      id: null,
      nombreTitular: "",
      cedulaTitular: "",
      infoExtra: "",
      pagina: 1
    };

    function renderPaginaFormulario() {
      const miembros = formularioContexto.tipo === "general"
        ? datos
        : datos.filter(d => d.coordId === formularioContexto.id);

      const porPagina = 10;
      const inicio = (formularioContexto.pagina - 1) * porPagina;
      const fin = inicio + porPagina;

      document.getElementById("formCoordNombre").innerText = formularioContexto.nombreTitular || "---";
      document.getElementById("formCoordCedula").innerText = formularioContexto.cedulaTitular || "---";
      document.getElementById("formCoordTelefono").innerText = formularioContexto.infoExtra || "---";

      const tbody = document.getElementById("tablaFormularioMiembrosBody");
      let html = "";

      for (let i = 0; i < porPagina; i++) {
        const idxReal = inicio + i;
        const m = miembros[idxReal];
        const bgRow = i % 2 === 0 ? "bg-white" : "bg-sky-50/50";
        const cenDir = m ? (m.centroDireccion || m.centroVotacion || m.recinto || "") : "";
        html += `
          <tr class="${bgRow} border-b border-blue-900/20">
            <td class="p-2.5 border-r border-blue-800/30 font-black text-blue-950 text-center">${idxReal + 1}.</td>
            <td class="p-2.5 border-r border-blue-800/30 font-bold text-slate-900">${m ? m.nombre : ""}</td>
            <td class="p-2.5 border-r border-blue-800/30 font-mono text-xs text-slate-700">${m ? m.cedula : ""}</td>
            <td class="p-2.5 border-r border-blue-800/30 text-slate-700">${m ? m.telefono : ""}</td>
            <td class="p-2.5 border-r border-blue-800/30 text-slate-700 font-semibold text-blue-900">${cenDir}</td>
            <td class="p-2.5 font-semibold text-blue-900">${m ? (m.mesaElectoral || "") : ""}</td>
          </tr>
        `;
      }

      tbody.innerHTML = html;
      document.getElementById("formResumenMiembros").innerText = `Total de miembros inscritos en este equipo: ${miembros.length}`;
      document.getElementById("formHojaIndicador").innerText = `Hoja ${formularioContexto.pagina} (Militantes ${inicio + 1} - ${fin})`;
      document.getElementById("btnFormAnterior").disabled = formularioContexto.pagina <= 1;
    }

    function abrirFormularioConDatos(nombreTitular, cedulaTitular, infoExtra, tipo, id) {
      formularioContexto = {
        tipo,
        id,
        nombreTitular,
        cedulaTitular,
        infoExtra,
        pagina: 1
      };
      renderPaginaFormulario();
      document.getElementById("modalFormularioMiembros").classList.remove("hidden");
    }

    function cambiarPaginaFormulario(delta) {
      const nuevaPag = formularioContexto.pagina + delta;
      if (nuevaPag < 1) return;
      formularioContexto.pagina = nuevaPag;
      renderPaginaFormulario();
    }

    function refrescarFormularioMiembrosAbierto() {
      renderPaginaFormulario();
    }

    function agregarMilitanteDesdeFormulario() {
      const coordTarget = formularioContexto.tipo === "coord"
        ? formularioContexto.id
        : (rolActual && rolActual.startsWith("coord_") ? rolActual : null);
      abrirModalNuevo(coordTarget);
    }

    function verFormularioCoordinador(coordId) {
      if (!tieneControlTotal() && coordId !== rolActual) return;
      const c = coordinadores[coordId];
      if (!c) return;
      abrirFormularioConDatos(c.nombre, c.cedula || "---", c.zona || "---", "coord", coordId);
    }

    function verFormularioUsuario(userId) {
      const u = usuarios.find(x => x.id === userId);
      if (!u) return;
      if (u.rol && coordinadores[u.rol]) {
        if (!tieneControlTotal() && u.rol !== rolActual) return;
        const c = coordinadores[u.rol];
        abrirFormularioConDatos(c.nombre, c.cedula || u.cedula, c.zona || u.rolLabel, "coord", u.rol);
      } else {
        if (!tieneControlTotal()) return;
        // Directora o Administrador: muestra todos los miembros del padrón bajo su dirección
        abrirFormularioConDatos(u.nombreCompleto, u.cedula, `${u.rolLabel} (Padrón General)`, "general", null);
      }
    }

    function cerrarModalFormularioMiembros() {
      document.getElementById("modalFormularioMiembros").classList.add("hidden");
    }

    function imprimirFormularioMiembros() {
      const contenido = document.getElementById("formularioMiembrosContenido").innerHTML;
      const printArea = document.getElementById("printArea");
      printArea.innerHTML = `<div class="max-w-4xl mx-auto p-4 bg-white text-slate-900">${contenido}</div>`;
      printArea.classList.remove("hidden");
      window.print();
      setTimeout(() => printArea.classList.add("hidden"), 500);
    }

    // =========================================================================
    // PASE DE LISTA POR REUNIONES (EXCLUSIVO ADMINISTRADORES)
    // =========================================================================
    let reuniones = JSON.parse(localStorage.getItem("padron_reuniones_v1")) || [
      {
        id: "reu_inicial",
        titulo: "Reunión General de Coordinación",
        fecha: new Date().toISOString().split("T")[0],
        hora: "18:00",
        lugar: "Local Central",
        asistencia: {},
        notas: {}
      }
    ];

    function guardarReunionesDB() {
      localStorage.setItem("padron_reuniones_v1", JSON.stringify(reuniones));
    }

    function obtenerConvocadosAsistencia() {
      const listaCoords = Object.values(coordinadores).map(c => ({
        key: "coord_" + c.id,
        tipo: "coordinadores",
        nombre: c.nombre,
        cedula: c.cedula || "---",
        detalleRol: `Coordinador (${c.zona})`
      }));
      const listaMilitantes = datos.map(d => {
        const c = coordinadores[d.coordId] || { nombre: "Sin Asignar" };
        return {
          key: "mil_" + d.id,
          tipo: "militantes",
          nombre: d.nombre,
          cedula: d.cedula,
          detalleRol: `Militante · ${c.nombre}`
        };
      });
      return [...listaCoords, ...listaMilitantes];
    }

    function cargarSelectReuniones() {
      if (!tieneControlTotal()) return;
      const sel = document.getElementById("selectReunionAsistencia");
      if (!sel) return;
      const prev = sel.value;
      if (reuniones.length === 0) {
        sel.innerHTML = `<option value="">-- Cree una reunión arriba --</option>`;
        return;
      }
      sel.innerHTML = reuniones.map(r =>
        `<option value="${r.id}">${r.fecha} ${r.hora ? r.hora : ""} — ${r.titulo}${r.lugar ? " (" + r.lugar + ")" : ""}</option>`
      ).join("");
      if (reuniones.some(r => r.id === prev)) {
        sel.value = prev;
      }
    }

    async function crearReunion(e) {
      e.preventDefault();
      if (!tieneControlTotal()) return;
      const titulo = document.getElementById("reunionTitulo").value.trim();
      const fecha = document.getElementById("reunionFecha").value;
      const hora = document.getElementById("reunionHora").value;
      const lugar = document.getElementById("reunionLugar").value.trim();

      let nueva = {
        id: "reu_" + Date.now(),
        titulo,
        fecha,
        hora,
        lugar,
        asistencia: {},
        notas: {}
      };

      if (modoConectado) {
        try {
          const res = await fetch(`${API_BASE}/reuniones.php`, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              action: "create",
              titulo, fecha, hora, lugar,
              operador: usuarioAutenticado.username,
              operadorRol: usuarioAutenticado.rolLabel
            })
          });
          const json = await res.json();
          if (json.success && json.data) {
            nueva = json.data;
          }
        } catch (err) {
          console.error("Error al crear reunión en servidor:", err);
        }
      }

      reuniones.unshift(nueva);
      guardarReunionesDB();
      document.getElementById("reunionTitulo").value = "";
      document.getElementById("reunionLugar").value = "";
      cargarSelectReuniones();
      document.getElementById("selectReunionAsistencia").value = nueva.id;
      registrarEventoBitacora(usuarioAutenticado.username, usuarioAutenticado.rolLabel, `Creó reunión: ${titulo} (${fecha})`);
      renderPaseDeLista();
    }

    async function eliminarReunionActual() {
      if (!tieneControlTotal()) return;
      const sel = document.getElementById("selectReunionAsistencia");
      const id = sel ? sel.value : "";
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      if (confirm(`¿Seguro que desea eliminar la reunión "${r.titulo}" (${r.fecha})?`)) {
        if (modoConectado) {
          try {
            await fetch(`${API_BASE}/reuniones.php`, {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({
                action: "delete",
                id,
                operador: usuarioAutenticado.username,
                operadorRol: usuarioAutenticado.rolLabel
              })
            });
          } catch (err) {
            console.error("Error al eliminar reunión en servidor:", err);
          }
        }
        reuniones = reuniones.filter(x => x.id !== id);
        guardarReunionesDB();
        cargarSelectReuniones();
        renderPaseDeLista();
      }
    }

    function marcarAsistencia(personaKey, estado) {
      if (!tieneControlTotal()) return;
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      r.asistencia[personaKey] = estado;
      guardarReunionesDB();
      renderPaseDeLista();

      if (modoConectado) {
        fetch(`${API_BASE}/reuniones.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "mark_asistencia",
            reunionId: id,
            personaKey,
            estado
          })
        }).catch(err => console.error("Error sincronizando asistencia:", err));
      }
    }

    function guardarNotaAsistencia(personaKey, nota) {
      if (!tieneControlTotal()) return;
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      r.notas[personaKey] = nota;
      guardarReunionesDB();

      if (modoConectado) {
        fetch(`${API_BASE}/reuniones.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "save_nota",
            reunionId: id,
            personaKey,
            nota
          })
        }).catch(err => console.error("Error sincronizando nota:", err));
      }
    }

    function marcarTodosAsistencia(estado) {
      if (!tieneControlTotal()) return;
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      const convocados = obtenerConvocadosAsistencia();
      const keys = convocados.map(p => p.key);
      convocados.forEach(p => {
        r.asistencia[p.key] = estado;
      });
      guardarReunionesDB();
      renderPaseDeLista();

      if (modoConectado) {
        fetch(`${API_BASE}/reuniones.php`, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            action: "mark_todos",
            reunionId: id,
            keys,
            estado
          })
        }).catch(err => console.error("Error marcando todos en servidor:", err));
      }
    }

    function renderPaseDeLista() {
      if (!tieneControlTotal()) return;
      const tbody = document.getElementById("tablaPaseDeLista");
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      const convocados = obtenerConvocadosAsistencia();

      if (!r) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400 font-semibold">Cree o seleccione una reunión para iniciar el pase de lista.</td></tr>`;
        document.getElementById("kpiAsisTotal").innerText = "0";
        document.getElementById("kpiAsisPresentes").innerText = "0";
        document.getElementById("kpiAsisTarde").innerText = "0";
        document.getElementById("kpiAsisAusentes").innerText = "0";
        document.getElementById("kpiAsisPct").innerText = "0%";
        actualizarGraficosAsistencia();
        return;
      }

      let presentes = 0, tarde = 0, ausentes = 0;
      convocados.forEach(p => {
        const est = r.asistencia[p.key] || "ausente";
        if (est === "presente") presentes++;
        else if (est === "tarde") tarde++;
        else ausentes++;
      });
      const total = convocados.length;
      const pct = total > 0 ? Math.round(((presentes + tarde) / total) * 100) : 0;

      document.getElementById("kpiAsisTotal").innerText = total;
      document.getElementById("kpiAsisPresentes").innerText = presentes;
      document.getElementById("kpiAsisTarde").innerText = tarde;
      document.getElementById("kpiAsisAusentes").innerText = ausentes;
      document.getElementById("kpiAsisPct").innerText = pct + "%";

      const texto = (document.getElementById("buscarAsistencia").value || "").toLowerCase();
      const tipoFiltro = document.getElementById("filtroTipoAsistencia").value;

      const filtrados = convocados.filter(p => {
        if (tipoFiltro !== "todos" && p.tipo !== tipoFiltro) return false;
        if (texto && !p.nombre.toLowerCase().includes(texto) && !p.cedula.toLowerCase().includes(texto)) return false;
        return true;
      });

      if (filtrados.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="p-8 text-center text-slate-400 font-semibold">No se encontraron personas con los filtros aplicados.</td></tr>`;
        return;
      }

      tbody.innerHTML = filtrados.map((p, idx) => {
        const est = r.asistencia[p.key] || "ausente";
        const nota = r.notas[p.key] || "";
        const clsPres = est === "presente" ? "bg-emerald-600 text-white shadow" : "bg-emerald-50 text-emerald-700 hover:bg-emerald-100";
        const clsTarde = est === "tarde" ? "bg-amber-500 text-white shadow" : "bg-amber-50 text-amber-700 hover:bg-amber-100";
        const clsAus = est === "ausente" ? "bg-rose-600 text-white shadow" : "bg-rose-50 text-rose-700 hover:bg-rose-100";
        const badgeTipo = p.tipo === "coordinadores"
          ? `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-indigo-100 text-indigo-800">${p.detalleRol}</span>`
          : `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">${p.detalleRol}</span>`;

        return `
          <tr class="hover:bg-slate-50 transition">
            <td class="p-3.5 text-center font-black text-slate-500">${idx + 1}</td>
            <td class="p-3.5 font-bold text-slate-900">${p.nombre}</td>
            <td class="p-3.5 font-mono text-xs">${p.cedula}</td>
            <td class="p-3.5">${badgeTipo}</td>
            <td class="p-3.5 text-center whitespace-nowrap">
              <div class="inline-flex gap-1.5">
                <button type="button" onclick="marcarAsistencia('${p.key}', 'presente')" class="px-2.5 py-1 rounded-lg text-xs font-extrabold transition ${clsPres}">
                  <i class="fa-solid fa-check mr-1"></i>Presente
                </button>
                <button type="button" onclick="marcarAsistencia('${p.key}', 'tarde')" class="px-2.5 py-1 rounded-lg text-xs font-extrabold transition ${clsTarde}">
                  <i class="fa-solid fa-clock mr-1"></i>Tarde/Excusa
                </button>
                <button type="button" onclick="marcarAsistencia('${p.key}', 'ausente')" class="px-2.5 py-1 rounded-lg text-xs font-extrabold transition ${clsAus}">
                  <i class="fa-solid fa-xmark mr-1"></i>Ausente
                </button>
              </div>
            </td>
            <td class="p-3.5">
              <input type="text" value="${nota.replace(/"/g, '&quot;')}" onchange="guardarNotaAsistencia('${p.key}', this.value)" placeholder="Observación..." class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:outline-none">
            </td>
          </tr>
        `;
      }).join("");
      actualizarGraficosAsistencia();
    }

    function exportarAsistenciaExcel() {
      if (!tieneControlTotal()) return;
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      const convocados = obtenerConvocadosAsistencia();
      const filas = convocados.map((p, idx) => ({
        "#": idx + 1,
        "Nombre Completo": p.nombre,
        "Cédula": p.cedula,
        "Rol / Coordinación": p.detalleRol,
        "Asistencia": (r.asistencia[p.key] || "ausente").toUpperCase(),
        "Observación": r.notas[p.key] || ""
      }));
      if (typeof XLSX !== "undefined") {
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(filas);
        XLSX.utils.book_append_sheet(wb, ws, "Pase_de_Lista");
        XLSX.writeFile(wb, `Asistencia_${r.fecha}_${r.titulo.replace(/\s+/g, "_")}.xlsx`);
      }
    }

    let chartAsistenciaPersonasInstance = null;
    let chartAsistenciaReunionesInstance = null;

    function actualizarGraficosAsistencia() {
      if (!tieneControlTotal()) return;
      const canvasPersonas = document.getElementById("chartAsistenciaPersonas");
      const canvasReuniones = document.getElementById("chartAsistenciaReuniones");
      const tbodyConfronte = document.getElementById("tablaConfronteAsistencia");
      if (!canvasPersonas || !canvasReuniones || !tbodyConfronte) return;

      const esTemaOscuro = document.body.classList.contains("theme-negro");
      const textColor = esTemaOscuro ? "#f1f5f9" : "#334155";
      const gridColor = esTemaOscuro ? "rgba(148, 163, 184, 0.15)" : "rgba(148, 163, 184, 0.25)";

      const tipoFiltro = (document.getElementById("filtroGraficaAsistencia") || {}).value || "todos";
      const convocados = obtenerConvocadosAsistencia().filter(p => tipoFiltro === "todos" || p.tipo === tipoFiltro);
      const totalReuniones = reuniones.length;

      // 1. Calcular acumulado por persona en todas las reuniones
      const resumenPersonas = convocados.map(p => {
        let presentes = 0, tarde = 0, ausentes = 0;
        reuniones.forEach(r => {
          const est = (r.asistencia && r.asistencia[p.key]) ? r.asistencia[p.key] : "ausente";
          if (est === "presente") presentes++;
          else if (est === "tarde") tarde++;
          else ausentes++;
        });
        const pct = totalReuniones > 0 ? Number((((presentes + tarde * 0.5) / totalReuniones) * 100).toFixed(1)) : 0;
        const pctReal = totalReuniones > 0 ? Math.round(((presentes + tarde) / totalReuniones) * 100) : 0;
        return {
          ...p,
          presentes,
          tarde,
          ausentes,
          pct,
          pctReal
        };
      }).sort((a, b) => (b.presentes - a.presentes) || (b.tarde - a.tarde) || a.nombre.localeCompare(b.nombre));

      // Tabla de Ranking / Confronte
      if (resumenPersonas.length === 0 || totalReuniones === 0) {
        tbodyConfronte.innerHTML = `<tr><td colspan="9" class="p-6 text-center text-slate-400 font-semibold">Cree al menos una reunión para visualizar el confronte de asistencia.</td></tr>`;
      } else {
        tbodyConfronte.innerHTML = resumenPersonas.map((p, idx) => {
          let badge = `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-rose-100 text-rose-800">Baja</span>`;
          let barColor = "bg-rose-500";
          if (p.pctReal >= 75) {
            badge = `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800">Constante</span>`;
            barColor = "bg-emerald-500";
          } else if (p.pctReal >= 50) {
            badge = `<span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-amber-100 text-amber-800">Regular</span>`;
            barColor = "bg-amber-500";
          }
          return `
            <tr class="hover:bg-slate-50 transition">
              <td class="p-3 text-center font-black text-indigo-600">#${idx + 1}</td>
              <td class="p-3 font-bold text-slate-900">${p.nombre}</td>
              <td class="p-3 text-xs text-slate-600">${p.detalleRol}</td>
              <td class="p-3 text-center font-bold">${totalReuniones}</td>
              <td class="p-3 text-center font-black text-emerald-600">${p.presentes}</td>
              <td class="p-3 text-center font-black text-amber-600">${p.tarde}</td>
              <td class="p-3 text-center font-black text-rose-600">${p.ausentes}</td>
              <td class="p-3">
                <div class="flex items-center gap-2">
                  <div class="w-20 h-2.5 bg-slate-200 rounded-full overflow-hidden">
                    <div class="h-full ${barColor} rounded-full" style="width:${p.pctReal}%"></div>
                  </div>
                  <span class="font-extrabold text-xs">${p.pctReal}%</span>
                </div>
              </td>
              <td class="p-3">${badge}</td>
            </tr>
          `;
        }).join("");
      }

      // Gráfico 1: Barras apiladas por persona (Top 15 para legibilidad)
      const topPersonas = resumenPersonas.slice(0, 15);
      if (chartAsistenciaPersonasInstance) chartAsistenciaPersonasInstance.destroy();
      chartAsistenciaPersonasInstance = new Chart(canvasPersonas.getContext("2d"), {
        type: "bar",
        data: {
          labels: topPersonas.map(p => p.nombre),
          datasets: [
            {
              label: "Presentes",
              data: topPersonas.map(p => p.presentes),
              backgroundColor: "#10b981",
              borderRadius: 4
            },
            {
              label: "Tarde / Excusa",
              data: topPersonas.map(p => p.tarde),
              backgroundColor: "#f59e0b",
              borderRadius: 4
            },
            {
              label: "Ausentes",
              data: topPersonas.map(p => p.ausentes),
              backgroundColor: "#f43f5e",
              borderRadius: 4
            }
          ]
        },
        options: {
          indexAxis: "y",
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            x: {
              stacked: true,
              beginAtZero: true,
              ticks: { stepSize: 1, color: textColor, font: { weight: "bold" } },
              grid: { color: gridColor }
            },
            y: {
              stacked: true,
              ticks: { color: textColor, font: { weight: "bold", size: 11 } },
              grid: { display: false }
            }
          },
          plugins: {
            legend: {
              position: "top",
              labels: { color: textColor, font: { weight: "bold", size: 11 } }
            }
          }
        }
      });

      // Gráfico 2: Evolución de % de asistencia por reunión (orden cronológico)
      const reunionesOrdenadas = [...reuniones].reverse();
      const labelsReuniones = reunionesOrdenadas.map(r => `${r.fecha} (${r.titulo.slice(0, 16)})`);
      const pctsReuniones = reunionesOrdenadas.map(r => {
        if (convocados.length === 0) return 0;
        let asistieron = 0;
        convocados.forEach(p => {
          const est = (r.asistencia && r.asistencia[p.key]) ? r.asistencia[p.key] : "ausente";
          if (est === "presente" || est === "tarde") asistieron++;
        });
        return Math.round((asistieron / convocados.length) * 100);
      });

      if (chartAsistenciaReunionesInstance) chartAsistenciaReunionesInstance.destroy();
      chartAsistenciaReunionesInstance = new Chart(canvasReuniones.getContext("2d"), {
        type: "line",
        data: {
          labels: labelsReuniones,
          datasets: [{
            label: "% de Asistencia",
            data: pctsReuniones,
            borderColor: "#4f46e5",
            backgroundColor: "rgba(79, 70, 229, 0.15)",
            borderWidth: 3,
            tension: 0.35,
            fill: true,
            pointRadius: 5,
            pointBackgroundColor: "#4f46e5"
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              max: 100,
              ticks: { callback: v => v + "%", color: textColor, font: { weight: "bold" } },
              grid: { color: gridColor }
            },
            x: {
              ticks: { color: textColor, font: { weight: "bold", size: 10 } },
              grid: { display: false }
            }
          },
          plugins: {
            legend: {
              position: "top",
              labels: { color: textColor, font: { weight: "bold", size: 11 } }
            }
          }
        }
      });
    }

    function imprimirPaseDeLista() {
      if (!tieneControlTotal()) return;
      const id = document.getElementById("selectReunionAsistencia").value;
      const r = reuniones.find(x => x.id === id);
      if (!r) return;
      const convocados = obtenerConvocadosAsistencia();
      const filasHtml = convocados.map((p, idx) => {
        const est = (r.asistencia[p.key] || "ausente").toUpperCase();
        return `
          <tr class="border-b border-slate-300">
            <td class="p-2 text-center font-bold">${idx + 1}</td>
            <td class="p-2 font-bold">${p.nombre}</td>
            <td class="p-2 font-mono">${p.cedula}</td>
            <td class="p-2">${p.detalleRol}</td>
            <td class="p-2 font-bold">${est}</td>
            <td class="p-2">${r.notas[p.key] || ""}</td>
          </tr>
        `;
      }).join("");

      const printArea = document.getElementById("printArea");
      printArea.innerHTML = `
        <div class="max-w-4xl mx-auto p-6 bg-white text-slate-900">
          <div class="border-b-2 border-indigo-950 pb-3 mb-4 flex justify-between items-center">
            <div>
              <h1 class="text-lg font-black uppercase text-indigo-950">ACTA DE PASE DE LISTA — ${r.titulo}</h1>
              <p class="text-xs text-slate-600">Fecha: ${r.fecha} ${r.hora ? "| Hora: " + r.hora : ""} ${r.lugar ? "| Lugar: " + r.lugar : ""}</p>
            </div>
            <div class="text-right text-xs font-bold">
              <p>Dirigido por: ${usuarioAutenticado ? usuarioAutenticado.nombreCompleto : "Administración"}</p>
            </div>
          </div>
          <table class="w-full text-left text-xs border border-slate-300">
            <thead class="bg-slate-100 border-b border-slate-300">
              <tr>
                <th class="p-2 text-center">#</th>
                <th class="p-2">Nombre Completo</th>
                <th class="p-2">Cédula</th>
                <th class="p-2">Rol / Coordinación</th>
                <th class="p-2">Estado</th>
                <th class="p-2">Observación</th>
              </tr>
            </thead>
            <tbody>${filasHtml}</tbody>
          </table>
        </div>
      `;
      printArea.classList.remove("hidden");
      window.print();
      setTimeout(() => printArea.classList.add("hidden"), 500);
    }


    async function inicializarConexionServidor() {
      const badge = document.getElementById("dbStatusBadge");
      const text = document.getElementById("dbStatusText");
      const loginStatus = document.getElementById("dbLoginStatus");

      try {
        const res = await fetch(`${API_BASE}/bootstrap.php`);
        if (!res.ok) throw new Error("HTTP " + res.status);
        const json = await res.json();

        if (json.success && json.data) {
          modoConectado = true;
          if (json.data.coordinadores && Object.keys(json.data.coordinadores).length > 0) {
            coordinadores = json.data.coordinadores;
          }
          if (json.data.usuarios && json.data.usuarios.length > 0) {
            usuarios = json.data.usuarios;
          }
          if (json.data.afiliados) {
            datos = json.data.afiliados;
          }
          if (json.data.reuniones) {
            reuniones = json.data.reuniones;
          }
          if (json.data.bitacora) {
            bitacoraAccesos = json.data.bitacora;
          }

          localStorage.setItem("padron_coordinadores_v2", JSON.stringify(coordinadores));
          localStorage.setItem("padron_usuarios_v2", JSON.stringify(usuarios));
          localStorage.setItem("padron_partido_db", JSON.stringify(datos));
          localStorage.setItem("padron_reuniones_v1", JSON.stringify(reuniones));
          localStorage.setItem("padron_bitacora_v2", JSON.stringify(bitacoraAccesos));

          if (badge && text) {
            badge.className = "hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40";
            text.innerText = "MySQL Hostinger (En Línea)";
          }
          if (loginStatus) {
            loginStatus.className = "text-[10px] font-bold text-emerald-300 bg-emerald-950/70 px-2 py-0.5 rounded-full border border-emerald-500/30";
            loginStatus.innerHTML = '<i class="fa-solid fa-database text-emerald-400 mr-1"></i> MySQL Hostinger (En Línea)';
          }

          renderCredencialesLogin();
          actualizarSelectoresCoordinadores();
          if (usuarioAutenticado) {
            actualizarTodoElSistema();
          }
          return true;
        }
      } catch (err) {
        console.warn("Base de datos remota no disponible o no configurada aún, funcionando en modo local:", err.message);
        if (badge && text) {
          badge.className = "hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40";
          text.innerText = "Modo Local (Offline)";
        }
        if (loginStatus) {
          loginStatus.className = "text-[10px] font-bold text-amber-300 bg-amber-950/70 px-2 py-0.5 rounded-full border border-amber-500/30";
          loginStatus.innerHTML = '<i class="fa-solid fa-server text-amber-400 mr-1"></i> Modo Local (Offline)';
        }
      }
      return false;
    }

    window.addEventListener("DOMContentLoaded", async () => {
      aplicarColorFondoGuardado();
      renderCredencialesLogin();
      actualizarSelectoresCoordinadores();
      await inicializarConexionServidor();
    });

  </script>
</body>
</html>
