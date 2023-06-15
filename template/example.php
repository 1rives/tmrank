<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <title>TMRank</title>

    <?php include_once('template/head_styles.php'); ?>

    <!-- Page-specific styles -->
    <!-- STYLES -->

  </head>
<body>

  <?php include_once('template/navbar.php'); ?>
  
  <?php 
    // Page content
    
    // Specific or general page content

  ?>    

  <?php // Used for pages with tables with/without login searching ?>    
  <div class="container is-max-widescreen"> 
      <div class="box">
          
          <?php include_once('template/login_form.php'); ?>

          <div class="mx-6 my-2 px-6" id="general"></div>
          
      </div>
  </div>
  
  
  <?php include_once('template/footer.php'); ?>

  <?php include_once('template/body_scripts.php'); ?>
  <!-- Additional scripts below this line -->
  <!-- SCRIPTS -->
</body>
</html>
