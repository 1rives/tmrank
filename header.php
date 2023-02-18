<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand"
       href="<?php echo $_SERVER['PHP_SELF'] . "?c=" . base64_encode("home") . "&a=" . base64_encode("goHome"); ?>">Control-Access</a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#">
        <i class="fas fa-bars"></i>
    </button>
    <!-- Navbar-->
    <ul class="navbar-nav ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown"
               aria-haspopup="true" aria-expanded="false">
                <?php echo $nombre; ?><i class="fas fa-user fa-fw"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">

                <a class="dropdown-item"
                   href="<?php echo $_SERVER['PHP_SELF'] . "?c=" . base64_encode("users") . "&a=" . base64_encode("goToChangePassword"); ?>">Cambiar
                    Contraseña</a>

                <div class="dropdown-divider"></div>
                <a class="dropdown-item"
                   href="<?php echo $_SERVER['PHP_SELF'] . "?c=" . base64_encode("logout") . "&a=" . base64_encode("logout"); ?>">Salir</a>
            </div>
        </li>
    </ul>
</nav>