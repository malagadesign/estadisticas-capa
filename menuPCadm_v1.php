<!-- Main Menu area start-->
<div class="main-menu-area mg-tb-40">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
					<?PHP $active = ''; if ($qm == 'usuarios'){$active = 'class="active"';} ?>
					<li <?PHP echo $active; ?>><a data-toggle="tab" href="#usuarios"><i class="notika-icon notika-support"></i> Usuarios</a>
					</li>
					<?PHP $active = ''; if ($qm == 'adm'){$active = 'class="active"';} ?>
					<li <?PHP echo $active; ?>><a data-toggle="tab" href="#configuracion"><i class="notika-icon notika-edit"></i> Configuración</a>
					</li>
					<?PHP $active = ''; if ($qm == 'ver'){$active = 'class="active"';} ?>
					<li <?PHP echo $active; ?>><a data-toggle="tab" href="#datos"><i class="notika-icon notika-bar-chart"></i> Encuestas</a>
					</li>
					<?PHP $active = ''; if ($qm == 'cuenta'){$active = 'class="active"';} ?>
					<li <?PHP echo $active; ?>><a data-toggle="tab" href="#cuenta"><i class="notika-icon notika-avable"></i> Cuenta</a>
					</li>
					<li><a><i class="notika-icon notika-support"></i> Usuario: <?PHP echo $_SESSION['ScapaUsuario'];?></a>
					</li>
				</ul>
				<div class="tab-content custom-menu-content">
					<?PHP $active = ''; if ($qm == 'usuarios'){$active = 'active';} ?>
					<div id="usuarios" class="tab-pane <?PHP echo $active; ?> notika-tab-menu-bg animated flipInX">
						<ul class="notika-main-menu-dropdown">
							<?PHP $active = ''; if ($qh == 'admUsuarios'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=usuarios&qh=admUsuarios" <?PHP echo $active; ?>>Administrativos</a>
							</li>
							<?PHP $active = ''; if ($qh == 'admSocios'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=usuarios&qh=admSocios" <?PHP echo $active; ?>>Socios</a>
							</li>
						</ul>
					</div>
					<?PHP $active = ''; if ($qm == 'adm'){$active = 'active';} ?>
					<div id="configuracion" class="tab-pane <?PHP echo $active; ?> notika-tab-menu-bg animated flipInX">
						<ul class="notika-main-menu-dropdown">
							<?PHP $active = ''; if ($qh == 'admRubros'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=adm&qh=admRubros" <?PHP echo $active; ?>>Rubros</a>
							</li>
							<?PHP $active = ''; if ($qh == 'admFamilias'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=adm&qh=admFamilias" <?PHP echo $active; ?>>Familias</a>
							</li>
							<?PHP $active = ''; if ($qh == 'admArticulos'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=adm&qh=admArticulos" <?PHP echo $active; ?>>Artículos</a>
							</li>
							<?PHP $active = ''; if ($qh == 'admMercados'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=adm&qh=admMercados" <?PHP echo $active; ?>>Mercados</a>
							</li>
							<?PHP $active = ''; if ($qh == 'admEncuestas'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=adm&qh=admEncuestas" <?PHP echo $active; ?>>Encuestas</a>
							</li>
						</ul>
					</div>
					<?PHP $active = ''; if ($qm == 'ver'){$active = 'active';} ?>
					<div id="datos" class="tab-pane <?PHP echo $active; ?> notika-tab-menu-bg animated flipInX">
						<ul class="notika-main-menu-dropdown">
							<?PHP $active = ''; if ($qh == 'ultimo'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=ver&qh=ultimo" <?PHP echo $active; ?>>Última</a>
							</li>
							<?PHP $active = ''; if ($qh == 'anteriores'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=ver&qh=anteriores" <?PHP echo $active; ?>>Anteriores</a>
							</li>
						</ul>
					</div>
					<?PHP $active = ''; if ($qm == 'cuenta'){$active = 'active';} ?>
					<div id="cuenta" class="tab-pane <?PHP echo $active; ?> notika-tab-menu-bg animated flipInX">
						<ul class="notika-main-menu-dropdown">
							<?PHP $active = ''; if ($qh == 'cambioPas'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=cuenta&qh=cambioPas" <?PHP echo $active; ?>>Cambiar contraseña</a>
							</li>
							<?PHP $active = ''; if ($qh == 'Cerrar'){$active = 'style="color: #9D4EDD;"';} ?>
							<li><a href="?qm=cuenta&qh=cerrar&cr=1" <?PHP echo $active; ?>>Cerrar sesión</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Main Menu area End-->
