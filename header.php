<?php
// header.php
// Este archivo es el punto de entrada para todas las páginas.
// Inicia la conexión a la base de datos (que a su vez inicia la sesión).
require_once 'conexion.php'; 

// --- MEJORA CRÍTICA: DEFINIR LA URL BASE ---
// Esto asegura que todos los enlaces, imágenes y CSS funcionen sin importar
// en qué carpeta nos encontremos (ej. /admin/ o la raíz).
// Si cambias el nombre de la carpeta de tu proyecto, solo necesitas cambiarlo aquí.
define('BASE_URL', '/insectos_excel/');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- El título de la página será dinámico. -->
    <title><?php echo $page_title ?? 'Enciclopedia de Artrópodos'; ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Nuestra hoja de estilos personalizada (con la ruta corregida) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/estilos.css">
</head>
<body class="bg-light">

    <header class="header-main sticky-top">
        <?php if (isset($_SESSION['user_id'])): 
            // Obtenemos el rol Y el avatar del usuario en una sola consulta
            $stmt_user_nav = $pdo->prepare("SELECT rol, avatar FROM usuarios WHERE id = ?");
            $stmt_user_nav->execute([$_SESSION['user_id']]);
            $currentUser = $stmt_user_nav->fetch();

            // Obtenemos las notificaciones no leídas
            $stmt_notif_count = $pdo->prepare("SELECT COUNT(*) FROM notificaciones WHERE usuario_id = ? AND leido = 0");
            $stmt_notif_count->execute([$_SESSION['user_id']]);
            $unread_notifications_count = $stmt_notif_count->fetchColumn();

            // Obtenemos una vista previa de las últimas notificaciones
            $stmt_notif_preview = $pdo->prepare("SELECT * FROM notificaciones WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 5");
            $stmt_notif_preview->execute([$_SESSION['user_id']]);
            $notifications_preview = $stmt_notif_preview->fetchAll();
        ?>
        <!-- BARRA DE NAVEGACIÓN PARA USUARIOS LOGUEADOS -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <!-- El logo ahora siempre apunta al portal.php -->
                <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>portal.php">
                    <i class="bi bi-bug-fill me-2 fs-3 text-success"></i>
                    <div>
                        <span class="fw-bold fs-5">Enciclopedia</span>
                        <small class="d-block text-light opacity-75" style="font-size: 0.75rem;">Artrópodos Interactiva</small>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        
                        <!-- ***** SISTEMA DE NOTIFICACIONES ***** -->
                        <li class="nav-item dropdown">
                            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell-fill"></i>
                                <?php if ($unread_notifications_count > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    <?php echo $unread_notifications_count; ?>
                                </span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><h6 class="dropdown-header">Notificaciones</h6></li>
                                <?php if (empty($notifications_preview)): ?>
                                    <li><p class="dropdown-item text-muted small">No hay notificaciones nuevas.</p></li>
                                <?php else: ?>
                                    <?php foreach ($notifications_preview as $notif): ?>
                                        <li>
                                            <a class="dropdown-item small" href="<?php echo BASE_URL . ($notif['link'] ?? 'notificaciones.php'); ?>">
                                                <?php echo htmlspecialchars($notif['mensaje']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-center small" href="<?php echo BASE_URL; ?>notificaciones.php">Ver todas</a></li>
                            </ul>
                        </li>

                        <!-- Menú de Usuario con AVATAR CORREGIDO -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <!-- LÓGICA MEJORADA PARA MOSTRAR AVATAR O INICIAL -->
                                <?php if (!empty($currentUser['avatar']) && file_exists('uploads/avatars/' . $currentUser['avatar'])): ?>
                                    <img src="<?php echo BASE_URL; ?>uploads/avatars/<?php echo htmlspecialchars($currentUser['avatar']); ?>" 
                                         alt="Avatar" 
                                         class="nav-avatar me-2"
                                         style="width: 40px !important; height: 40px !important; border-radius: 50% !important; object-fit: cover !important; border: 2px solid #27ae60 !important;">
                                <?php else: ?>
                                    <div class="nav-avatar-initial me-2" 
                                         style="width: 40px !important; height: 40px !important; border-radius: 50% !important; background-color: #27ae60 !important; color: white !important; display: flex !important; align-items: center !important; justify-content: center !important; font-size: 16px !important; font-weight: bold !important;">
                                        <span><?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?></span>
                                    </div>
                                <?php endif; ?>
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>portal.php"><i class="bi bi-grid-fill me-2"></i>Portal Principal</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>perfil.php"><i class="bi bi-person-circle me-2"></i>Mi Perfil</a></li>
                                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>registros.php"><i class="bi bi-journal-text me-2"></i>Mis Registros</a></li>
                                <?php if ($currentUser && $currentUser['rol'] === 'admin'): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-warning" href="<?php echo BASE_URL; ?>admin/"><i class="bi bi-speedometer2 me-2"></i>Panel de Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <?php else: ?>
        <!-- BARRA DE NAVEGACIÓN PARA VISITANTES -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <!-- El logo para visitantes ahora apunta a la página de login -->
                <a class="navbar-brand d-flex align-items-center" href="<?php echo BASE_URL; ?>login.php">
                    <i class="bi bi-bug-fill me-2 fs-3 text-success"></i>
                    <div>
                        <span class="fw-bold fs-5 text-dark">Enciclopedia</span>
                        <small class="d-block text-muted" style="font-size: 0.75rem;">Artrópodos Interactiva</small>
                    </div>
                </a>
                <div class="d-flex align-items-center">
                    <a href="<?php echo BASE_URL; ?>login.php" class="btn btn-outline-primary me-2"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar Sesión</a>
                    <a href="<?php echo BASE_URL; ?>registro.php" class="btn btn-success"><i class="bi bi-person-plus me-1"></i>Registrarse</a>
                </div>
            </div>
        </nav>
        <?php endif; ?>
    </header>

    <!-- SECCIÓN DE BOTÓN DE REGRESO AUTOMÁTICO -->
    <?php 
    // Mostramos el botón en todas las páginas, excepto en el portal mismo.
    if (basename($_SERVER['PHP_SELF']) !== 'portal.php' && isset($_SESSION['user_id'])): 
    ?>
    <div class="sub-header bg-white border-bottom shadow-sm py-2">
        <div class="container text-end">
            <a href="<?php echo BASE_URL; ?>portal.php" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver al Portal
            </a>
        </div>
    </div>
    <?php endif; ?>

    <main class="container my-4">
        <!-- Mensajes de alerta (se mostrarán aquí) -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['message']['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['message']['text']; unset($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>