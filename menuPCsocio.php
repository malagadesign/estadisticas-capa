<!-- Main Menu area start-->
<div class="main-menu-area mg-tb-40">
	<div class="container">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
					<?PHP $active = ''; if ($qm == 'ver'){$active = 'class="active"';} ?>
					<li <?PHP echo $active; ?>><a data-toggle="tab" href="#datos"><i class="notika-icon notika-bar-chart"></i> Encuestas</a>
					</li>
					<li><a href="?cr=1"><i class="notika-icon notika-close"></i> Cerrar sesión</a>
					</li>
					<li><a><i class="notika-icon notika-support"></i> Usuario: <?PHP echo $_SESSION['ScapaUsuario'];?></a>
					</li>
				</ul>
				<div class="tab-content custom-menu-content">
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
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Main Menu area End-->
