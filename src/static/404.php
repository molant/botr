<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Page Not Found - botr.io</title>

  <style>
  body, html {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100vw;
    height: 100vh;
  }

  #main-content {
    font-family: sans-serif;
    box-sizing: border-box;
    padding: 15px;
  }

  h1 {
    margin: 0;
    font-size: 28px;
    line-height: 36px;
  }

  .smiley { color: #86ad49; }

  p {
    margin: 0;
    margin-top: 14px;
    font-size: 14px;
    line-height: 18px;
    color: rgba(0, 0, 0, .54);
  }
  </style>
</head>

<body>

  <div id="main-content">
    <h1><span class="smiley">:/</span> There is nothing to see here</h1>
    <p>You will be <a href="<?php echo esc_url(home_url()); ?>">redirected to the front page</a> (<span id="counter">5</span>).</p>
  </div>

  <script>

  <?php
  $analytics_code = get_theme_mod('botr_analytics_code');

  if ($analytics_code !== '' && $analytics_code !== false):

    ?>
    (function(e,t,n,i,s,a,c){e[n]=e[n]||function(){(e[n].q=e[n].q||[]).push(arguments)}
    ;a=t.createElement(i);c=t.getElementsByTagName(i)[0];a.async=true;a.src=s
    ;c.parentNode.insertBefore(a,c)
  })(window,document,"galite","script","https://cdn.jsdelivr.net/npm/ga-lite@2/dist/ga-lite.min.js");
  galite('create', '<?php echo $analytics_code; ?>', 'auto');
  galite('send', 'pageview');

  <?php endif; ?>

  (function(z,y,x) {
    y.setInterval(function(){if(z<0){return}else{if(!z){y.location.href=x}document.getElementById('counter').textContent=z--;}},1000)
  })(5,window,'<?php echo esc_url(home_url()); ?>');

  </script>



</body>

</html>
