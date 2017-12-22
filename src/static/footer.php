
</div>

<?php wp_footer(); ?>

<?php
$analytics_code = get_theme_mod('botr_analytics_code');

// Google Analytics Code will be printed only if a Tracking Code is defined
// Remember to flush cache when modifying the code
// Using galite
// https://github.com/jehna/ga-lite
if ($analytics_code !== '' && $analytics_code !== false):
?>

<script>
(function(e,t,n,i,s,a,c){e[n]=e[n]||function(){(e[n].q=e[n].q||[]).push(arguments)}
;a=t.createElement(i);c=t.getElementsByTagName(i)[0];a.async=true;a.src=s
;c.parentNode.insertBefore(a,c)
})(window,document,"galite","script","https://cdn.jsdelivr.net/npm/ga-lite@2/dist/ga-lite.min.js");
galite('create', '<?php echo $analytics_code; ?>', 'auto');
galite('send', 'pageview');
</script>

<?php endif; ?>

</body>

</html>
