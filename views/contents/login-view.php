<div class="full-box cover containerLogin d-flex align-items-center justify-content-center" 
     style="min-height: 100vh; background: url('<?php echo SERVERURL; ?>views/assets/img/bg-clinic.jpg') no-repeat center center/cover;">

    <form action="" method="POST" autocomplete="off" 
          class="full-box logInForm p-5 rounded-4 bg-white shadow" 
          style="max-width: 420px; width: 100%; border: 1px solid #ffffffff;">

        <figure class="text-center mb-3">
            <img src="<?php echo SERVERURL; ?>views/assets/img/logo.png" 
                 alt="<?php echo COMPANY; ?>" 
                 class="img-fluid" style="max-width: 120px;">
        </figure>

        <h5 class="text-center text-dark fw-bold mb-4" style="font-size: 1.3rem;">
            <?php echo COMPANY; ?>
        </h5>

        <!-- Usuario -->
        <div class="form-group label-floating mb-4">
            <label class="control-label" for="loginUserName" style="font-size: 1.1rem;font-weight: 500;">👤 Usuario</label>
            <input class="form-control border-primary rounded-3" 
                   value="Manu20177" id="loginUserName" type="text" name="loginUserName" >
        </div>

        <!-- Contraseña -->
        <div class="form-group label-floating mb-4" style="position: relative;">
            <label class="control-label" for="loginUserPass" style="font-size: 1.1rem;font-weight: 500;">🔒 Contraseña</label>
            <input class="form-control border-primary rounded-3" 
                   value="Manolo1998*" id="loginUserPass" type="password" name="loginUserPass" >
            <span onclick="togglePasswordLogin()" 
                  style="position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: #ffffffff;">
                <i id="icon-login-password" class="zmdi zmdi-eye"></i>
            </span>
        </div>

        <!-- Botón -->
        <div class="form-group text-center mt-4">
            <input type="submit" value="Iniciar sesión" 
                   class="btn btn-info w-100 py-2 rounded-3 fw-bold" style="font-size: 1.1rem; color: #fff;">
        </div>

    </form>
</div>

<?php 
    if(isset($_POST['loginUserName'])){
        require_once "./controllers/loginController.php";
        $log = new loginController();
        echo $log->login_session_start_controller();
    }
?>

<script>
function togglePasswordLogin() {
  const input = document.getElementById('loginUserPass');
  const icon = document.getElementById('icon-login-password');

  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('zmdi-eye');
    icon.classList.add('zmdi-eye-off');
  } else {
    input.type = 'password';
    icon.classList.remove('zmdi-eye-off');
    icon.classList.add('zmdi-eye');
  }
}
</script>

<style>
/* Estilo general */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f8f9fa;
}

/* Labels y textos */
.control-label {
    font-weight: bold;
    color: #000 !important; /* color negro */
    font-size: 1.1rem;
}

/* Evitar que el label cambie de color al hacer focus */
.form-group.label-floating .form-control:focus + .control-label,
.form-group.label-floating.is-focused .control-label {
    color: #000 !important;
}

/* Inputs */
.form-control {
    padding: 10px 12px;
    border: 1px solid #6a99e0ff;
    border-radius: 0.5rem;
	font-weight: 400;
	color: #000;

}

/* Botón */
.btn-info {
	background: linear-gradient(135deg, #0d6efd, #20c997);
	border: none;
	color: #fff;
	font-weight: bold;
}
.btn-info:hover {
	background: linear-gradient(135deg, #20c997, #0d6efd);
	color: #fff;
}
</style>
