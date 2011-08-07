<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<meta http-equiv="content-language" content="es" />
	<meta name="robots" content="noindex,nofollow" />
	<title>Sistema de Información para la Gestión de Actividades</title>
	<link type="image/x-icon" href="<?php echo BASE_PATH;?>/img/favicon.ico" rel="icon"/>
	<?php echo $html->includeCss('template/base');?>	
	<?php echo $html->includeCss('template/themes/activo-2/style');?>	
	<?php echo $html->includeCss("jquery-ui/Aristo/jquery-ui-1.8.7.custom");?>		
	<?php echo $html->includeCss('custom');?> <!-- css de edición -->	
	<?php 
	/*
	 * Códigos CSS propios de cada vista. Es necesario declarar
	 * la variable (en la función del controlador) como makecss
	 * e ingresar los css en ésta como un array.
	 * ej:
	 * $this->set('makecss',array('elemento_1','elemento_n'));
	 */	
	if(isset($makecss) && is_array($makecss) && count($makecss)>0){
		
		foreach($makecss as $printcss){			
			echo "\t".$html->includeCss($printcss)."\n";			
		}		
	}
    ?>		
    <?php if(isset($make_tag_css)) echo $html->css_tag($make_tag_css);?>		
    <?php echo $html->javascript_tag("\n\t\tvar url_project = \"". BASE_PATH ."/\";");?>	
	<?php echo $html->includeJs("jquery-1.6.2.min");?>		
	<?php echo $html->includeJs("jquery-ui-1.8.14.custom.min");?>		
	<?php echo $html->includeJs("custom");?>		
	<?php 
	if(isset($makejs) && is_array($makejs) && count($makejs)>0){
		
		foreach($makejs as $printjs){			
			echo "\t".$html->includeJs($printjs)."\n";			
		}		
	}	
    ?>	
	<?php if(isset($make_tag_js)) echo $html->javascript_tag($make_tag_js);?>	
	<?php 
	/*
	 * Agregar HTML al div 'sidebar'
	 * Declarar variable 'make_sidebar' y en ésta
	 * agregar el contenido a mostrar.
	 */
	if(isset($make_sidebar) && strlen($make_sidebar)!=0){
		?>
		<script type="text/javascript">
		<!--
			$('#sidebar').append('<?php echo $make_sidebar?>');
		//-->
		</script>
		<?php 
	}
		
	?>		
</head>
<body>	
	<div id="container">
		<div id="header">
			<h1><?php echo $html->link('Bienestar Universitario','');?></h1>
			<div id="user-navigation">
				<ul class="wat-cf">
					<li><a href="#">Configuración</a></li>
					<li><a class="logout" href="<?php echo BASE_PATH . '/' . 'dashboards/logout'?>">Salir</a></li>
				</ul>
			</div>
			<div id="main-navigation">
				<ul class="wat-cf">
					<?php include_once 'main_navigation.php';?>	
				</ul>
			</div>			
		</div>
		<div id="wrapper" class="wat-cf">
			<div id="main">			
			
				<div class="block" id="block-text">
          			<div class="secondary-navigation">
            			<ul class="wat-cf">
              				<?php include_once 'secondary_navigation.php';?>
            			</ul>
          			</div>
          			<div class="content">
	            		<div class="inner">         			
    	        	
			
			
			
			