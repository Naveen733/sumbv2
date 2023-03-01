<header class="header-mobile d-block d-lg-none">

    <div class="header-mobile__bar">
        <div class="container-fluid">
            <div class="header-mobile-inner">
                <a class="logo" href="user-index.php">
                    <img src="images/brand-web.webp" alt="CoolAdmin" style="max-height:65px;" />
                </a>

                <button class="hamburger hamburger--slider" type="button">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>

            </div>
            <nav class="navbar-mobile">

                    <ul class="navbar-mobile__list list-unstyled">
                        <li><a href="/dashboard"><i class="fas fa-home"></i>Dashboard</a></li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#"><i class="fas fa-briefcase"></i>Registration <i class="fas fa-angle-down"></i></a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list" style="display:none;">
                                <li><a href="#">ABN</a></li>
                                <li><a href="#">Company</a></li>
                                <li><a href="#">Business Name</a></li>
                                <li><a href="#">Trust</a></li>
                            </ul>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#"><i class="fa-solid fa-money-bill-transfer"></i>Transactions <i class="fas fa-angle-down"></i></a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li><a href="/expense">Expense</a></li>
                                <li><a href="/invoice">Invoice</a></li>
                                <li><a href="#">Report</a></li>
                                <li><a href="/invoice/settings">Invoice Settings</a></li>
                                <li><a href="/chart-accounts">Chart of Accounts</a></li>
                            </ul>
                        </li>
                        <li><a href="#"><i class="fas fa-folder"></i>Employment Docs</a></li>
                        <li><a href="#"><i class="fas fa-calendar-alt"></i>Calendar</a></li>
                        <li><a href="#"><i class="far fa-check-square"></i>Request <span class="badge badge-danger">10</span></a></li>
                        <li><a href="/fileupload"><i class="fas fa-files-o"></i>Files</a></li>
                        <li><a href="/logout" onclick="return confirm('Are you sure you want to log out?')"><i class="zmdi zmdi-power"></i>Logout</a></li>
                    </ul>

            </nav>
        </div>
    </div>
    
</header>
<!-- END HEADER MOBILE-->



<!-- MENU SIDEBAR-->
<aside class="menu-sidebar d-none d-lg-block">
    <div class="sumb--logo">
        <a href="#"><img src="images/brand-web.webp" alt="Cool Admin" style="max-height:75px;" /></a>
    </div>

    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li><a href="/dashboard"><i class="fas fa-home"></i>Dashboard</a></li>
                <li class="has-sub">
                    <a class="js-arrow" href="#"><i class="fas fa-briefcase"></i>Registration <i class="fas fa-angle-down"></i></a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li><a href="#">ABN</a></li>
                        <li><a href="#">Company</a></li>
                        <li><a href="#">Business Name</a></li>
                        <li><a href="#">Trust</a></li>
                    </ul>
                </li>
                <li class="has-sub">
                    <a class="js-arrow" href="#"><i class="fa-solid fa-money-bill-transfer"></i>Transactions <i class="fas fa-angle-down"></i></a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list">
                        <li><a href="/expense">Expense</a></li>
                        <li><a href="/invoice">Invoice</a></li>
                        <li><a href="#">Report</a></li>
                        <li><a href="/invoice/settings">Invoice Settings</a></li>
                        <li><a href="/chart-accounts">Chart of Accounts</a></li>
                    </ul>
                </li>
                <li><a href="#"><i class="fas fa-folder"></i>Employment Docs</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i>Calendar</a></li>
                <li><a href="#"><i class="far fa-check-square"></i>Request <span class="badge badge-danger">10</span></a></li>
                <li><a href="/doc-upload"><i class="fas fa-files-o"></i>Files</a></li>
                <li><a href="/logout" onclick="return confirm('Are you sure you want to log out?')"><i class="zmdi zmdi-power"></i>Logout</a></li>
            </ul>
        </nav>
    </div>
</aside>
<!-- END MENU SIDEBAR-->