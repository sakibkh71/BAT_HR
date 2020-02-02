<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-logo">
        @php($previlege_house = session()->get('PRIVILEGE_HOUSE'))
        {!!  getHouseLogo() !!}
    </div>
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            @inject('menu', 'App\Http\Controllers\MenuManagement')
            {!! $menu->menuList() !!}
        </ul>
    </div>
</nav>
