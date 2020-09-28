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
                    <a href="./index.php" target="_self">
                         <img src="{{TEMPLATEURL}}/images/logo.png" alt="Netpolgár" class="toplogo">
                    </a>
                </span>                
            </div>
        </div>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav nav-dropdown" data-app-modern-menu="true">
				<li class="nav-item">
                    <a class="nav-link link text-white display-4" 
                       href="{{MYDOMAIN}}/opt/groups/list/parentid/0/userid/0" target="_self">{{txt('GROUPS')}}</a>
                </li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" 
						target="_self" data-toggle="dropdown-submenu" aria-expanded="false">{{txt('PROJECTS')}}</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4"
							href="{{MYDOMAIN}}/opt/projects/list/filterstate//userid/0" target="_self">{{txt('ALL')}}</a>
						<a class="text-white dropdown-item display-4" 
							href="{{MYDOMAIN}}/opt/projects/list/filterstate/active/userid/0" target="_self">{{txt('ACTIVE')}}</a>
						<a class="text-white dropdown-item display-4" 
							href="{{MYDOMAIN}}/opt/projects/list/filterstate/draft/userid/0" target="_self">{{txt('DRAFT')}}</a>
						<a class="text-white dropdown-item display-4" 
							href="{{MYDOMAIN}}/opt/projects/list/filterstate/proposal/userid/0" target="_self">{{txt('PROPOSAL')}}</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" 
						data-toggle="dropdown-submenu" aria-expanded="false">{{txt('MARKET')}}</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('OFFER')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('DEMAND')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('TRANSACTIONS')}}</a>
					</div>
				</li>
				<li class="nav-item">
					<a class="nav-link link text-white display-4" href="#" target="_self">{{txt('DOCS')}}</a>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" 
						data-toggle="dropdown-submenu" aria-expanded="false">{{txt('DISPUTES')}}</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('CHATS')}}k</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('POLLS')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('DECISIONS')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('MESSAGES')}}</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" href="#" 
						target="_self" data-toggle="dropdown-submenu" aria-expanded="false">{{txt('EVENTS')}}</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('FOLLOWS')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('OLDS')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">{{txt('ALL')}}</a>
					</div>
				</li>
				<li class="nav-item dropdown">
					<a class="nav-link link text-white dropdown-toggle display-4" 
					    href="#"  target="_self" data-toggle="dropdown-submenu" aria-expanded="false">
					    {{txt('SETUP')}}
					</a>
					<div class="dropdown-menu">
						<a class="text-white dropdown-item display-4" href="#" target="_self">
						{{txt('CATEGORIES')}}</a>
						<a class="text-white dropdown-item display-4" href="#" target="_self">
						{{txt('USERGROUPS')}}</a>
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
						   href="./index.php/opt/users/profile/id/{{loggedUser.id}}" target="_self">
						   {{txt('PROFILE')}}</a>
						<a class="text-white dropdown-item display-4" 
						   href="./index.php/opt/users/logout/id/{{loggedUser.id}}" target="_self">
						   {{txt('LOGOUT')}}</a>
					</div>
				</li>
				
			</ul>
         <div class="navbar-buttons mbr-section-btn" ng-if="loggedUser.id == 0">
         	<a class="btn btn-sm btn-white-outline display-4" 
         	   href="{{MYDOMAIN}}/opt/users/login" target="_self">{{txt('LOGIN')}}</a>
         </div>
        </div>
    </nav>
</section>

