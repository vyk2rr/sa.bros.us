<?
/* ===========================

  sabros.us monousuario version 1.8
  http://sabros.us/

  sabros.us is a free software licensed under GPL (General public license)

  =========================== */

	header("Content-type: text/html; charset=UTF-8");
	
	include("include/config.php");
	include("include/conex.php");
	include("include/functions.php");

	if (!esAdmin())
	{
		header("Location: login.php");		
		exit();
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$locale?>" lang="<?=$locale?>">
<head>
	<title><?=__("panel de control");?>/sabros.us</title>
	<meta name="generator" content="sabros.us <?=version();?>" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="<?=$Sabrosus->sabrUrl?>/sabor.css" type="text/css" />
	<link rel="shortcut icon" href="<?=$Sabrosus->sabrUrl?>/images/sabrosus_icon.png" />
	<script type="text/javascript" src="<?=$Sabrosus->sabrUrl;?>/include/mootools.js"></script>

	<script language="JavaScript" type="text/javascript">
		<!--			
			function elimina(id_enlace)
			{	// Algo de JavaScript para aquello de que no se quiera borrar.
				var x;
				x=window.confirm("<?=__("Realmente desea eliminar este enlace de su sabros.us?");?>\n\n<?=__("Este evento no se puede deshacer!");?>");				 
				if (x) { location="eliminar.php?id="+id_enlace+"&confirm=0"; }
			}
				
			var contenedor;
			window.onload = function() {
				contenedor = new Fx.Style('divContenedor', 'opacity', {duration: 5000, onComplete:
					function() {
						document.getElementById('divContenedor').style.display="none";
					}
				});
				contenedor.custom(1,0);
			}

		-->
		</script>
</head>
	
<body>
<div id="pagina">
	<div id="titulo">
		<h2>sabros.us/<span><?=__("panel de control");?></span></h2>
		<p class="submenu">
			<a href="editar.php"><?=__("agregar enlace");?></a> | 
			<a href="generarBadge.php"><?=__("generar badge");?></a> | 
			<a href="importar.php"><?=__("importar");?></a> |
			<a href="exportar.php"><?=__("exportar");?></a> | 
			<a href="opciones.php"><?=__("ir a opciones");?></a> | 
			<a href="index.php"><?=__("ir a sabros.us");?></a> | 
			<a href="close.php"><?=__("terminar la sesi&oacute;n");?></a>
		</p>
	</div>
	
	<div id="contenido">
		<? if (isset($_GET["er"])) { ?>
			<div id="divContenedor" class="error">
				<p><?=__("No es posible exportar enlaces, debido a que el directorio <code>tmp</code> no cuenta con permisos de escritura.");?></p>
			</div>
		<? } ?>

		<div id="formulario">
			<form action="cpanel.php" method="get" name="buscar">
				<fieldset>
					<label for="buscar"><?=__("Buscar:");?></label>				
					<input class="input_naranja" id="buscar" name="buscar" type="text" />				
					<input class="submit_normal" type="submit" value="<?=__("buscar");?>" name="btnBuscar" />
				</fieldset>
			</form>
		</div>
<?		
		$page = isset($_GET['pag'])?$_GET['pag']:"";
		if(isset($_GET['pag'])) $begin=$page*$Sabrosus->limit; else $begin=0;
		$aux = $begin+$Sabrosus->limit;
		if (isset($_GET["buscar"]))
		{
			$keywords = explode(" ", $_GET["buscar"]);
			$query = "SELECT id_enlace,title,enlace,descripcion FROM ".$prefix."sabrosus "."WHERE title LIKE '%".$keywords['0']."%' OR descripcion LIKE '%".$keywords['0']."%'";		
			for ($i=1; $i<count($keywords); $i++) 
			{
				$query .= " OR title LIKE '%".$keywords[$i]."%' OR descripcion LIKE '%".$keywords[$i]."%'";
			}	
			
			$query_next = $query." ORDER BY fecha DESC LIMIT $aux,$Sabrosus->limit";
			$query .= " ORDER BY fecha DESC LIMIT $begin,$Sabrosus->limit";
			
			$result = mysql_query($query,$link);
			$result_next = mysql_query($query_next,$link);
		}
		else
		{			
			$result = mysql_query("select * from ".$prefix."sabrosus ORDER BY fecha DESC LIMIT $begin,$Sabrosus->limit",$link);
			$result_next = mysql_query("select * from ".$prefix."sabrosus ORDER BY fecha DESC LIMIT $aux,$Sabrosus->limit");
		}
?>
		<table cellspacing="0">
			<thead>
				<tr><th colspan="4"><?=__("Control de contenidos");?></th></tr>
			</thead>
			
<?			while ($row = mysql_fetch_array($result))
				{					
				$privado = esPrivado($row['id_enlace'])?>					
			<tr>
				<td class="objeto"><?=($privado)? '<img src="images/lock.png" alt="'.__("Enlace Privado:").' '.$row["title"].'" title="'.__("Enlace Privado:").' '.$row["title"].'" />':'';?> <?=$row["title"]?></td>
				<td><a href="ir.php?id=<? echo $row["id_enlace"]; ?>"><img src="images/link.png" alt="<?=__("Ver");?>" title="<?=__("Ver");?>" /></a></td>
				<td><a href="editar.php?id=<? echo $row["id_enlace"]; ?>"><img src="images/edit.png" alt="<?=__("Editar");?>" title="<?=__("Editar");?>" /></a></td>
				<td><a href="eliminar.php?id=<?=$row["id_enlace"];?>&amp;confirm=1" onclick="elimina(<?=$row["id_enlace"];?>);return false;"><img src="images/delete.png" alt="<?=__("Eliminar");?>" title="<?=__("Eliminar");?>" /></a></td>
			</tr>
<?				}	?>
			<tr>
				<td colspan="4" class="paginator">
				<?
					if(isset($_GET['pag'])&&$_GET['pag']>0)
					{
						if(isset($_GET['buscar']))
							echo "<a class=\"alignleft\" href=\"cpanel.php?pag=".($page-1)."&amp;buscar=".$_GET['buscar']."\">&laquo; ".__("Anterior")."</a>";				
						else
							echo "<a class=\"alignleft\" href=\"cpanel.php?pag=".($page-1)."\">&laquo; ".__("Anterior")."</a>";
					}
					if(mysql_num_rows($result_next)>0)
					{
						if(isset($_GET['buscar']))
							echo "<a class=\"alignright\" href=\"cpanel.php?pag=".($page+1)."&amp;buscar=".$_GET['buscar']."\">".__("Siguiente")." &raquo;</a> ";
						else
							echo "<a class=\"alignright\" href=\"cpanel.php?pag=".($page+1)."\">".__("Siguiente")." &raquo;</a>";
					}
				?>
				</td>
			</tr>
		</table>
	</div>
	
	<div id="pie">
		<p class="powered"><?=__("generado con:")?>&nbsp;&nbsp;<a title="sabros.us" href="http://sabros.us/">sabros.us</a></p>
	</div>

</div>
</body>
</html>
