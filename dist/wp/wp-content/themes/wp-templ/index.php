<?php
$thisPageName = 'top';
include(APP_PATH.'libs/head.php');
?>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" href="<?php echo APP_ASSETS ?>css/page/top.min.css">
</head>
<body id="top" class='top'>
<!-- HEADER -->
<?php include(APP_PATH.'libs/header.php'); ?>
<main id="wrap">
	<?php include(APP_PATH.'libs/loading.php'); ?>
	<div class="visual">
		<div class="visual__bg" data-parallax='{"y": -70, "smoothness": 10}'></div>
		<!-- <video muted="" autoplay="" playsinline="" loop><source src="<?php //echo APP_ASSETS; ?>img/studio/mainvisual.mp4"></video> -->
	  <div class="visual">
	    <div class="visual__scroll">
	      <a href="#intro" class="visual__scroll--btn"><span>scroll</span></a>
	    </div>
	    <div class="visual__txt">
	      <picture>
	        <source srcset="<?php echo APP_ASSETS; ?>img/common/visual_txt_sp.svg" media="(max-width: 767px)">
	        <img src="<?php echo APP_ASSETS; ?>img/common/visual_txt.svg" alt="Amiida">
	      </picture>
	    </div>
	  </div>
	</div>
	<div class="intro" id="intro">
		<div class="wcm">
			<div class="intro__img inview fadeInBottom">
				<img src="<?php echo APP_ASSETS; ?>img/top/img_intro.jpg" alt="">
			</div>
			<div class="intro__info inview fadeInBottom fadeInBottomDelay">
				<h3 class="intro__info--ttl">94%の会員様に、アミーダのヨガを通じて、<br>「心と体の変化」を感じて頂いてます。</h3>
			</div>
		</div>
		<div class="intro__txt inview fadeInBottom">
			<div class="wcm">
				<img src="<?php echo APP_ASSETS; ?>img/top/logo.jpg" alt="Amiida" class="logo">
				<p class="intro__txt--ttl">アミーダの想い</p>
				<p class="intro__txt--txt">アミーダの名前には「無量の光」という意味が込められています。<br>またロゴにある蓮の花は、泥水の中でも美しい花を咲かせます。<br><br>現代社会で仕事、家事、育児など「ストレス」を感じる事も多く、<br>泥水に浸かってしまうような時もありますが、<br class="pc">アミーダを通じて、心身ともに蓮の花のように美しく輝いていく<br>女性になってほしい、という想いを名前とロゴにこめております。<br><br>「心も、体も、美しく変化した」と思っていただける方を<br class="pc">日本全国に、一人でも多く増やしていきたい。<br><br>それが私たちの想いです。</p>
			</div>
		</div>
	</div>
	<div class="anchor inview fadeInBottom">
		<div class="wcm">
			<a href="<?php echo APP_URL; ?>amiida_life/" class="anchor__item">
				<span class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/anchor01.jpg)"></span>
				<span class="inner">
					<p class="anchor__item--ttl">アミーダのある生活</p>
					<p class="anchor__item--txt">Ami-ida life</p>
				</span>
			</a>
			<a href="https://www.ami-ida.com/yoganyogalp/" class="anchor__item">
				<span class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/anchor02.jpg)"></span>
				<span class="inner">
					<p class="anchor__item--ttl">溶岩石ホットヨガとは</p>
					<p class="anchor__item--txt">Features</p>
				</span>
			</a>
			<a href="<?php echo APP_URL; ?>studio/" class="anchor__item">
				<span class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/anchor03.jpg)"></span>
				<span class="inner">
					<p class="anchor__item--ttl">お近くのスタジオを探す</p>
					<p class="anchor__item--txt">Studio</p>
				</span>
			</a>
		</div>
	</div>
	<?php
	  $wp_news = new WP_Query();
	  $param_news = array(
	    'post_type'=>'news',
			'order' => 'DESC',
			'showposts' => 3,
			'meta_key' => 'date',
			'orderby' => 'meta_value',
			'order' => 'DESC',
			'meta_query' => array(
				array(
					'key' => 'studio',
					'value' => '',
					'compare' => '='
				)
			)
	  );
	  $wp_news->query($param_news);
	  if($wp_news->have_posts()){
	?>
	<div class="c-news inview fadeInBottom">
	  <div class="wcm">
	    <p class="c-news__ttl">NEWS</p>
	    <div class="c-news__lst">
	    <?php while($wp_news->have_posts()){
	      $wp_news->the_post();?>
	        <div data-id="<?php echo get_the_ID(); ?>" class="c-news__lst--item">
	          <p class="date"><?php echo date('Y/m/d', strtotime(get_field('date')));?></p>
	          <p class="cat"><em>お知らせ</em></p>
	          <p class="ttl"><em><?php echo get_the_title();?></em></p>
	        </div>
	      <?php } ?>
	    </div>
	  </div>
	</div>
	<?php } wp_reset_postdata();?>
	<div class="c-banner inview fadeInBottom on">
		<div class="wcm">
			<a href="<?php echo APP_URL; ?>information/" class="img">
				<picture>
				  <source srcset="<?php echo APP_ASSETS; ?>img/top/banner_corona_sp.png" media="(max-width: 767px)">
				  <img src="<?php echo APP_ASSETS; ?>img/top/banner_corona.jpg" alt="安全衛生管理について">
				</picture>
			</a>
		</div>
	</div>
	<div class="feature1" id="feature">
		<div class="wcm">
			<h3 class="the-title inview fadeInBottom">アミーダ溶岩ホット<br class="sp">ヨガの特徴</h3>
			<div class="feature1__lst">
				<div class="feature1__lst--item inview fadeInBottom">
					<div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/feature1.jpg)"></div>
					<div class="info">
						<div class="inner">
							<p class="ttl">貴重な天然溶岩石を使用</p>
							<p class="txt">溶岩石の遠赤外線で体の芯から自然に温めるので、呼吸もしやすく体への負担も少ないです。<br>天然の溶岩石で温めるスタジオはミネラルとマイナスイオン、サラサラした大量の汗が噴き出します。</p>
						</div>
					</div>
				</div>
				<div class="feature1__lst--item inview fadeInBottom">
					<div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/feature2.jpg)"></div>
					<div class="info">
						<div class="inner">
							<p class="ttl">未経験でも安心のプログラム</p>
							<p class="txt">良質で経験豊富なインストラクターが、個人のレベルや、一人一人のお悩みに適した「パーソナル型サポート」を実施。１ヶ月ごとにカウンセリングの時間を設け、お悩みを解決していきます。</p>
						</div>
					</div>
				</div>
				<div class="feature1__lst--item inview fadeInBottom">
					<div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/feature3.jpg)"></div>
					<div class="info">
						<div class="inner">
							<p class="ttl">”美”と”健康”への高い効果</p>
							<p class="txt">身体を内側から温め、基礎体温が上がるのが溶岩ホットヨガの特徴です。美容・美肌やダイエットなどの効果だけでなく、生理痛、便秘、冷え性、肩こりなどの根本的な改善にも効果があります。</p>
						</div>
					</div>
				</div>
				<div class="feature1__lst--item inview fadeInBottom">
					<div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/feature4.jpg)"></div>
					<div class="info">
						<div class="inner">
							<p class="ttl">心の健康とリラックス効果</p>
							<p class="txt">よくいただくお客様のお声として、「イライラしなくなった」「心が穏やかになった」「よく眠れるようになった」と言うお声があります。家庭や職場とは違う、もう一つの私の居場所として、生活の一部としてご利用いただいています。</p>
						</div>
					</div>
				</div>
				<div class="feature1__lst--item inview fadeInBottom">
					<div class="img lazy" data-bg="url(<?php echo APP_ASSETS; ?>img/top/feature5.jpg)"></div>
					<div class="info">
						<div class="inner">
							<p class="ttl">キレイで清潔感のあるスタジオ</p>
							<p class="txt">毎レッスン後に換気と掃除を行うため、スタジオ内はとって もキレイです。</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="sec-check">
		<div class="the-title wcm inview fadeInBottom">アミーダはこんな方の<br class="sp">ライフスタイルを<br class="pc">整えるための、<br class="sp">溶岩ホットヨガスタジオです。</div>
		<div class="sec-check__bg inview fadeInBottom">
			<div class="sec-check__bg--lst">
				<div class="wcm">
					<ul>
						<li>体型を維持したい！でも激しい運動をするのはつらい・・</li>
						<li>体質を改善したい！肌の調子や体調をすぐにこわしてしまう・・</li>
						<li>家事や育児で疲れた・・ストレスでイライラしてしまう日々・・</li>
						<li>仕事で不規則な食生活や生活リズムになりがち・・</li>
						<li>運動が得意ではないから、継続できたことがない・・</li>
						<li>肩こりや腰痛つらい・・頭痛や生理痛もつらい・・</li>
						<li>ホットヨガの経験はあるけど、効果が感じられず長続きしなかった・・</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
  <div class="c-voice">
  	<div class="wcm">
			<h3 class="the-title wcm inview fadeInBottom">アミーダの溶岩ホットヨガを選ぶお客様のお声</h3>
  		<?php include(APP_PATH.'libs/voice.php'); ?>
	  	<a href="<?php echo APP_URL; ?>studio" class="c-btn inview fadeInBottom">お近くのスタジオを探す</a>
	  </div>
	</div>
	  <?php $studioarea = array(
	      'post_type'                => 'studio',
	      'orderby'                  => 'id',
	      'order'                    => 'DESC',
	      'hide_empty'               => 1,
	      'taxonomy'                 => 'studioarea' ,
	      'pad_counts'              => false,
	  );
	  $categories = get_categories( $studioarea );?>
	  <?php $studio = new WP_Query(array(
	    'post_type'       => 'studio',
	    'orderby'         => 'menu_order',
	    'order'           => 'ASC',
	    'showposts'       => -1,
	    'post_status'     => 'publish',
	  ));
	  if ($studio->have_posts()) :?>
	  <div class="studio wcm">
	  	<h3 class="the-title inview fadeInBottom">体験レッスン受付中の<br class="sp">スタジオ一覧</h3>
	  	<p class="studio__txt inview fadeInBottom">※店舗により体験内容・キャンペーン内容が異なります。<br>詳細は、 各店舗ページをご覧ください。</p>
	  	<div class="studio__select inview fadeInBottom">
	  		<select>
	  			<option value="">All</option>
	  			<?php foreach($categories as $cat){ ?>
	  			<option value="<?php echo $cat->name; ?>" <?php //if($cat->name == '東京'){echo "selected";} ?>><?php echo $cat->name; ?></option>
	  			<?php } ?>
	  		</select>
	  	</div>
	  	<div class="lst-studio inview fadeInBottom" id="list-studio">
				<?php while ($studio->have_posts()) : $studio->the_post();
		       $fields = get_fields();
		    ?>
	  		<div class="lst-studio__item">
	  			<div class="lst-studio__item--map">
	  				<iframe width="100" height="100" frameborder="0" src="https://maps.google.com/maps?q=<?php echo $fields['access_zipcode'].$fields['access_address01']; ?>&amp;hl=ja&amp;output=embed" allowfullscreen></iframe>
	  			</div>
	  			<div class="lst-studio__item--info">
	  				<p class="ttl"><?php echo get_the_title(); ?></p>
	  				<p class="txt">
	  					<?php echo $fields['access_zipcode'].'<br>'.$fields['access_address01']; ?>
	  					<?php  if(!empty($fields['access_tel'])){ echo '<br>'.$fields['access_address02'];} ?>
	  				</p>
	  				<?php if(!empty($fields['access_tel'])){ ?>
	  				<a href="tel:<?php echo $fields['access_tel']; ?>" class="txt">TEL：<?php echo $fields['access_tel']; ?></a>
	  				<?php } if(!empty($fields['access_fax'])){ ?>
	  				<p class="txt">FAX：<?php echo $fields['access_fax']; ?></p>
	  				<?php } ?>
	  				<div class="btn">
	  					<a href="<?php the_permalink(); ?>" class="btn-detail">店舗ページへ</a>
	  					<a target="_blank" href="https://maps.google.com/maps?q=<?php echo $fields['access_zipcode'].$fields['access_address01']; ?>&amp;hl=ja" class="btn-location">地図をみる</a>
	  				</div>
	  			</div>
	  		</div>
	  		<?php endwhile; ?>
	  	</div>
	  	<a href="<?php echo APP_URL; ?>studio/" class="c-btn inview fadeInBottom">もっとみる</a>
	  </div>
		<?php endif; ?>
	<?php include(APP_PATH.'libs/slider.php'); ?>
	<div class="faq">
		<?php include(APP_PATH.'libs/faq.php'); ?>
	</div>
	<div class="sns wcm">
		<h3 class="the-title inview fadeInBottom">SNS</h3>
		<div class="grBtn inview fadeInBottom">
			<a target="_blank" href="https://www.instagram.com/amiida_official/?hl=ja" class="grBtn__item ins"><p>Instagram</p></a>
		</div>
	</div>
	<div class="popup popup-new">
	</div>
</main>
<?php include(APP_PATH.'libs/footer.php'); ?>
<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="<?php echo APP_ASSETS ?>js/page/top.min.js"></script>
<script>
	var _url = "<?php echo APP_URL; ?>";
</script>
</body>
</html>