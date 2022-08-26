<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ $nav == 'home' ? 'active' : '' }}">
            <a class="nav-link" href="/">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">Jurnal</span>
            </a>
        <li class="nav-item">
            <a class="nav-link">
                <i class="icon-head menu-icon"></i>
                <span class="menu-title">Daftar Customer</span>
            </a>
        </li>
        <li class="nav-item {{ $nav == 'report' ? 'active' : '' }}">
            <a class="nav-link" href="/report">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">Laporan</span>
            </a>
        </li>
    </ul>
</nav>
