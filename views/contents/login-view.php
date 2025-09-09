<div class="full-box cover containerLogin">
	<form action="" method="POST" autocomplete="off" class="full-box logInForm shadow-lg p-4 rounded-4" style="max-width: 400px;  margin: 0 auto;">

		<figure class="full-box">
			<img src="<?php echo SERVERURL; ?>views/assets/img/logo.png" alt="<?php echo COMPANY; ?>" class="img-responsive" style="max-width: 100px; display: block; margin: 0 auto;">
		</figure>
		<p class="text-center text-muted text-uppercase"><?php echo COMPANY; ?></p>
		<div class="form-group label-floating">
		  <label class="control-label" for="loginUserName">Nombre de usuario</label>
		  <input class="form-control" value="Manu20177" id="loginUserName" type="text" name="loginUserName">
		  <p class="help-block">Escribe tú Usuario</p>
		</div>
		<div class="form-group label-floating" style="position: relative;">
			<label class="control-label" for="loginUserPass">Contraseña</label>
			<input class="form-control" value="Manolo1998*" id="loginUserPass" type="password" name="loginUserPass">
			<span onclick="togglePasswordLogin()" style="position: absolute; top: 5px; right: 10px; cursor: pointer;">
				<i id="icon-login-password" class="zmdi zmdi-eye"></i>
			</span>
			<p class="help-block">Escribe tú contraseña</p>
		</div>
		<div class="form-group text-center">
			<input type="submit" value="Iniciar sesión" class="btn btn-raised btn-info">
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
	/* Cuando el input o el grupo tenga la clase is-focused */
.form-group.is-focused .control-label {
    color: #fff !important;
}

</style>