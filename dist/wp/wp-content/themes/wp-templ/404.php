<?php
include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/404.min.css">
</head>
<body id="page404" class="page404">
  <!-- Header -->
  <?php include(APP_PATH.'/libs/header.php'); ?>
  <main>
    <section class="page_404 ">
      <h2 class="page-title text-center">4<em>0</em>4</h2>
      <div class='text-center'>
        <p class="txt">
          アクセスしようとしたページは、移動したか削除されました。<br class="pc">下記リンクに移動して下さい。
          <br><br>
        </p>
        <a class="c-btn" href="<?php echo esc_url( home_url( '/' ) )?>">HOME</a>
      </div>
    </section>
  </main>
  <!-- Footer -->
  <?php include(APP_PATH.'/libs/footer.php'); ?>
<!-- End Document
================================================== -->
</body>
</html>