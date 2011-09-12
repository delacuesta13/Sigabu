<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="content-language" content="es" />
	<meta name="robots" content="noindex,nofollow" />
	<title>Sistema de Información para la Gestión de Actividades</title>
	<link type="image/x-icon" href="<?php echo BASE_PATH;?>/img/favicon.ico" rel="icon"/>
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo BASE_PATH;?>/css/template/base.css"/>	
	<link rel="stylesheet" media="screen,projection" type="text/css" href="<?php echo BASE_PATH;?>/css/template/themes/activo-2/style.css"/>
</head>
<body>
	<div id="container">
		<div id="box">
			<h1>Sigabu</h1>
			<div class="block" id="block-login">
				<h2>Login</h2>
				<div class="content login">
					<div class="flash">
					<?php
					## error al autenticarse
					if (isset($error_login)) {
						## mostrar mensaje
						?>
						<div class="message <?php echo $error_login['type']?>">
							<p><?php echo $error_login['message']?></p>
						</div>
						<?php 
					} 
					?>
					</div>
					<form method="post" name="formulario" id="formulario" class="form login"
					action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action?>">
						<div class="group wat-cf">
							<div class="left">
								<label class="label right" for="usuario">Usuario</label>
							</div>
							<div class="right">
								<input type="text" class="text_field" name="usuario" id="usuario" 
								<?php
								if (isset($_POST['usuario'])) echo 'value="' . $_POST['usuario'] . '"'; 
								?>
								/>
							</div>
						</div>
						<div class="group wat-cf">
							<div class="left">
								<label class="label right" for="password">Password</label>
							</div>
							<div class="right">
								<input type="password" class="text_field" name="password" id="password"/>
							</div>
						</div>
						<div class="group navform wat-cf">
							<div class="right">
								<button class="button" type="submit">
									<img src="<?php echo BASE_PATH?>/img/icons/key.png" alt="Login"/> Iniciar sesión
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>	
			<div id="footer">
				<p>Copyright &copy; 2011 Jhon Adrián Cerón Guzmán.</p>
			</div>
		</div>
	</div>
</body>
</html>