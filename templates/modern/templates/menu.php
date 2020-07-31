  <section class="menu mainmenu cid-s2WHFAAIzf" once="menu" id="menu1-2">
    <nav class="navbar navbar-expand beta-menu navbar-dropdown align-items-center navbar-fixed-top navbar-toggleable-sm bg-color transparent">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </button>
        <div class="menu-logo">
            <div class="navbar-brand">
                <span class="navbar-logo">
                    <a href="./index.php">
                         <img src="{{TEMPLATEURL}}/images/logo.png" alt="Netpolgár" class="toplogo">
                    </a>
                </span>                
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-dropdown" data-app-modern-menu="true">
				<li class="nav-item">
                    <a class="nav-link link text-white display-4" 
                       href="{{MYDOMAIN}}/opt/groups/list" target="_self">Csoportok</a>
                </li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" 
					    href="#"  target="_self" data-toggle="dropdown-submenu" aria-expanded="false">Beállítások</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">Témakörök</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Felhasználói csoportok</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" target="_self" data-toggle="dropdown-submenu" aria-expanded="false">Projektek</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">Aktív</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Lezárt</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Tervezet</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" data-toggle="dropdown-submenu" aria-expanded="false">Piactér</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">Kínál</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Keres</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Tranzakciók</a>
					</div>
				</li>
				<li class="nav-item">
					<a class="nav-link link text-white display-4" href="#" target="_self">Dokumentumok</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" data-toggle="dropdown-submenu" aria-expanded="false">Eszmecserék</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">Viták</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Szavazások</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Döntések</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Privát üzenetek</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" target="_self" data-toggle="dropdown-submenu" aria-expanded="false">Események</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">Elkövetkezők</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Korábbiak</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">Összes</a>
					</div>
				</li>
				<li class="nav-item dropdown" ng-if="loggedUser.id > 0">
					<a class="nav-link link text-white dropdown-toggle display-4" 
					   href="#"  target="_self" data-toggle="dropdown-submenu" aria-expanded="false">
					   	<img src="{{loggedUser.avatar}}" id="navbarUserAvatar" 
					   	   title="{{loggedUser.nick}}" ng-if="loggedUser.avatar != ''"/>
					   	<span ng-if="loggedUser.avatar == ''">{{loggedUser.nick}}</span>
					</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" 
						   href="./index.php/opt/users/profile/id/{{loggedUser.id}}" target="_self">Profil</a>
						<a class="text-white dropdown-item display-4" 
						   href="./index.php/opt/users/logout/id/{{loggedUser.id}}" target="_self">Kijelentkezés</a>
					</div>
				</li>
				
			</ul>
         <div class="navbar-buttons mbr-section-btn" ng-if="loggedUser.id == 0">
         	<a class="btn btn-sm btn-white-outline display-4" 
         	   href="{{MYDOMAIN}}/opt/users/login" target="_self">Bejelentkezés</a>
         </div>
        </div>
    </nav>
</section>

