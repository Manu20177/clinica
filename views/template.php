<?php
// �7�2�1�5 No debe haber NADA antes de esta l��nea, ni espacios ni l��neas vac��as
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$actionsRequired = false;
 $getViews = new viewsController();
$response = $getViews->get_views_controller();

// Si es certificado-view.php, no incluir el template HTML
if (strpos($response, "certificado-view.php") !== false) {
    require_once $response;
    exit(); // Importante: evita seguir cargando HTML
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include "./views/inc/links.php"; ?>
</head>
<body>
    <?php 
       

        // Verifica si el archivo es certificado-view
        if (strpos($response, 'certificado-view.php') !== false) {
            require_once $response;
            exit(); // Para evitar que siga imprimiendo el HTML
        }
        if ($response == "login" || $response == "registro"):
            if ($response == "registro"):
                // Cargar la vista pública de registro
                require_once "./views/contents/registro-view.php";
            else:
                // Cargar el login normal
                require_once "./views/contents/login-view.php";
            endif;
            
       
        else:
            require_once "./controllers/loginController.php";
            /*---------- Check Access ----------*/
            $sc = new loginController();
            // Validar sesi��n antes de usar $_SESSION
            if (!isset($_SESSION['userToken'], $_SESSION['userName'])) {
                $sc->login_session_destroy_controller();
            }
            echo $sc->check_access($_SESSION['userToken'], $_SESSION['userName']);
            /*---------- Login Out ----------*/
            if (isset($_POST['token'])) {
                $logout = new loginController();
                $logout->login_session_destroy_controller();
            } 
            /*---------- SideBar ----------*/
            include "./views/inc/sidebar.php"; 
        ?>
        <!-- Content page -->
        <section class="full-box dashboard-contentPage">
            <?php
                /*---------- NavBar ----------*/
                include "./views/inc/navbar.php";
                /*---------- Include Contents ----------*/
                require_once $response;
            ?>
        </section>
    <?php endif; ?>
    <!--====== Scripts -->
    <?php include "./views/inc/scripts.php"; ?>
</body>
</html>