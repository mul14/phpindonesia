<div id="toolbar">
	<div class="container">
		<div class="row">

			{######################## Logo ########################}
			<div class="span3">
				<a href="/" id="logo"></a>
			</div>

			{######################## Top Menu ########################}
			<div class="span6">
				<ul id="topnav">
					<li><a href="/home">Beranda</a></li>
					<li><a href="/organization">Organisasi</a></li>
					<li><a href="/training">Pelatihan</a></li>
					<li><a href="/community">Komunitas</a></li>
					<li><a href="/career">Karir</a></li>
				</ul>
			</div>
			<div class="span3">
				{% if acl.isLogin == true %}
				<div class="dropdown pull-right">
					<div class="btn-group pull-right">
					<a class="btn btn-primary alert-block" href="/user/profile/{{ user.Uid }}"><img src="{{ user.Avatar }}?s=18&d=retro"/> {{ user.Name }}</a>
					<a class="btn btn-primary alert-block dropdown-toggle" data-toggle="dropdown" href="#"> <span class="caret"></span></a>
					<ul id="account" class="dropdown-menu" role="menu" aria-labelledby="drop1">
						<li><a href="/setting">Setelan</a></li>
						<li><a href="/auth/logout">Keluar</a></li>
					</ul>
					</div>
				</div>
				{% else %}
				<a href="/auth/login" class="btn btn-primary pull-right alert-block">Masuk</a>
				{% endif %}
			</div>
			
		</div>
	</div>
</div>