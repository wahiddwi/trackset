<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    @php $cur_site = Session::get('selected_site'); @endphp
    {{ $cur_site->si_site . ' - ' . $cur_site->si_name }}
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-left" style="min-width: 240px" id="navSiteDropdown">
    {{-- <input type="text" class="dropdown-header col-12 fas" style="outline:none;border:0"
      placeholder="&#xF002; Search Outlet" id="navSiteSelector"> --}}

    {{-- <input type="text" placeholder="Search Outlet" id="navSiteSelector"> --}}
    <div class="form col-12">
      <input class="input" placeholder="Search Outlet" type="text" id="navSiteSelector">
      <i class="fa fa-search"></i>
    </div>

    <div id="siteDropdown" style="overflow-x: hidden; height:40vh">
      @php $avail_site = Session::get('available_sites');@endphp
      @foreach ($avail_site as $site => $name)
        <a href="{{ route('changesite', ['site' => $site]) }}" class="dropdown-item">
          {{ $site . ' - ' . $name }}
        </a>
      @endforeach
    </div>
  </div>
</li>
