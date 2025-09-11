<?php
// Iniciar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detectar la ruta actual (solo el segmento final de la URL)
$currentPage = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Helper para menú activo
function isActiveMenu($pages, $currentPage) {
    return in_array($currentPage, $pages) ? "active-parent" : "";
}

// Helper para submenu abierto
function isOpenSubMenu($pages, $currentPage) {
    return in_array($currentPage, $pages) ? "show-menu" : "";
}
?>
<section class="full-box cover dashboard-sideBar">
	<div class="full-box dashboard-sideBar-bg btn-menu-dashboard"></div>
	<div class="full-box dashboard-sideBar-ct">
		<!-- SideBar Title -->
		<div class="full-box text-uppercase text-center text-titles dashboard-sideBar-title">
			<?php echo COMPANY; ?> 
			<i class="zmdi zmdi-close btn-menu-dashboard visible-xs"></i>
		</div>

		<!-- SideBar User info -->
		<div class="full-box dashboard-sideBar-UserInfo">
			<figure class="full-box">
				<img style="width: 125px; height: auto;" src="<?php echo SERVERURL; ?>views/assets/img/logo.png" alt="UserIcon">
				<figcaption class="text-center text-titles"><?php echo $_SESSION['userNombre']; ?></figcaption>
				<b><figcaption class="text-center text-titles"><?php echo $_SESSION['userType']; ?></figcaption></b>			
			</figure>
			<ul class="full-box list-unstyled text-center">
				<li>
					<a href="<?php echo SERVERURL; ?>userinfo/<?php echo $_SESSION['userKey']; ?>/" class="<?php echo ($currentPage=='userinfo')?'active':''; ?>">
						<i class="zmdi zmdi-assignment-account"></i>
					</a>
				</li>
				<li>
					<a href="<?php echo SERVERURL; ?>account/<?php echo $_SESSION['userKey']; ?>/" class="<?php echo ($currentPage=='account')?'active':''; ?>">
						<i class="zmdi zmdi-settings"></i>
					</a>
				</li>
				<li>
					<a href="#!" class="btnFormsAjax" data-action="logout" data-id="form-logout">
						<i class="zmdi zmdi-power"></i>
					</a>
				</li>
			</ul>
			<form action="" id="form-logout" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="token" value="<?php echo $_SESSION['userToken']; ?>">
			</form>
		</div>

		<!-- SideBar Menu -->
		<ul class="list-unstyled full-box dashboard-sideBar-Menu">
			<?php if($_SESSION['userType']=="Administrador"): ?>
			<li>
				<a href="<?php echo SERVERURL; ?>dashboard/" class="<?php echo ($currentPage=='dashboard')?'active':''; ?>">
					<i class="zmdi zmdi-view-dashboard zmdi-hc-fw"></i> Inicio
				</a>
			</li>

			<!-- Administradores -->
			<li class="<?php echo isActiveMenu(['admin','adminlist'], $currentPage); ?>">
				<a href="#!" class="btn-sideBar-SubMenu">
					<i class="zmdi zmdi-account zmdi-hc-fw"></i> Administradores <i class="zmdi zmdi-caret-down pull-right"></i>
				</a>
				<ul class="list-unstyled full-box <?php echo isOpenSubMenu(['admin','adminlist'], $currentPage); ?>">
					<li>
						<a href="<?php echo SERVERURL; ?>admin/" class="<?php echo ($currentPage=='admin')?'active':''; ?>">
							<i class="zmdi zmdi-account-add zmdi-hc-fw"></i> Nuevo
						</a>
					</li>
					<li>
						<a href="<?php echo SERVERURL; ?>adminlist/" class="<?php echo ($currentPage=='adminlist')?'active':''; ?>">
							<i class="zmdi zmdi-accounts zmdi-hc-fw"></i> Listado
						</a>
					</li>
				</ul>
			</li>

			<!-- Usuarios -->
			<li class="<?php echo isActiveMenu(['user','userlist'], $currentPage); ?>">
				<a href="#!" class="btn-sideBar-SubMenu">
					<i class="zmdi zmdi-face zmdi-hc-fw"></i> Usuarios <i class="zmdi zmdi-caret-down pull-right"></i>
				</a>
				<ul class="list-unstyled full-box <?php echo isOpenSubMenu(['user','userlist'], $currentPage); ?>">
					<li>
						<a href="<?php echo SERVERURL; ?>user/" class="<?php echo ($currentPage=='user')?'active':''; ?>">
							<i class="zmdi zmdi-account-circle zmdi-hc-fw"></i> Nuevo
						</a>
					</li>
					<li>
						<a href="<?php echo SERVERURL; ?>userlist/" class="<?php echo ($currentPage=='userlist')?'active':''; ?>">
							<i class="zmdi zmdi-male-female zmdi-hc-fw"></i> Listado
						</a>
					</li>
				</ul>
			</li>

			<li>
				<a href="<?php echo SERVERURL; ?>backup/" class="<?php echo ($currentPage=='backup')?'active':''; ?>">
					<i class="zmdi zmdi-tv-alt-play zmdi-hc-fw"></i> Backup
				</a>
			</li>

			<?php elseif($_SESSION['userType']=="Secretaria"): ?>
			<li>
				<a href="<?php echo SERVERURL; ?>home/" class="<?php echo ($currentPage=='home')?'active':''; ?>">
					<i class="zmdi zmdi-view-dashboard zmdi-hc-fw"></i> Inicio
				</a>
			</li>
			<li>
				<a href="<?php echo SERVERURL; ?>videonow/" class="<?php echo ($currentPage=='videonow')?'active':''; ?>">
					<i class="zmdi zmdi-tv-play zmdi-hc-fw"></i> prueba secretaria
				</a>
			</li>

			<?php else: ?>
			<li>
				<a href="<?php echo SERVERURL; ?>homeMedico/" class="<?php echo ($currentPage=='homeMedico')?'active':''; ?>">
					<i class="zmdi zmdi-view-dashboard zmdi-hc-fw"></i> Inicio
				</a>
			</li>
			<li>
				<a href="<?php echo SERVERURL; ?>videonow/" class="<?php echo ($currentPage=='videonow')?'active':''; ?>">
					<i class="zmdi zmdi-tv-play zmdi-hc-fw"></i> prueba medico
				</a>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</section>

<style>
	/* Link activo */
	.dashboard-sideBar-Menu a.active {
		background: #3F51B5;
		color: #fff !important;
		border-radius: 5px;
	}

	/* Padre activo */
	.dashboard-sideBar-Menu li.active-parent > a {
		background: #303F9F;
		color: #fff !important;
	}

	/* Submenús abiertos */
	.show-menu {
		display: block !important;
	}
</style>
