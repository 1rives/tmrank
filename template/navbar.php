<!-- Start Navigation bar -->
<header>
  <nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="container">

      <div class="navbar-brand">
        <!-- TMRank logo -->
        <a class="navbar-item" href="home.php">
          <img src="assets/img/logo.svg" width="112" height="28"></img>
        
          <!-- Hidden mobile navbar -->
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
          <span aria-hidden="true"></span>
        </a>
      </div>

      <!-- Navbar item lists -->
      <div class="navbar-menu">
        <div class="navbar-end">

          <!-- Home item -->
          <a class="navbar-item" href="home.php"> Home </a>

          <!-- Player ladder item -->
          <a class="navbar-item" href="players.php"> Search a player </a>

          <!-- Dropdown Ladder -->
          <div class="navbar-item is-hoverable">
            <a class="navbar-link is-arrowless"> Ladder </a>
            <div class="navbar-dropdown">
              <a class="navbar-item" href="world.php"> World ranking </a>
              <hr class="navbar-divider"/>
              <a class="navbar-item" href="zones.php"> Zones ranking </a>
            </div>
          </div>

          <!-- Dropdown More -->
          <div class="navbar-item has-dropdown is-hoverable">
            <a class="navbar-link is-primary"> More </a>

            <div class="navbar-dropdown">
              <a class="navbar-item" href="#"> About </button>
              <a class="navbar-item" href="#"> Faq </a>
              <a class="navbar-item" href="#"> Resources </a>
            </div>
          </div>
        </div>

       </div>
      </div>
    </div>
  </nav>
</header>
<!-- End Navigation bar -->