<include file="Public:header" />
<style type="text/css">
.center-block {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
.div-a{
text-align:center;
height:60px;
line-height:40px;
}
</style>
<div class="container">
		<present name="message">
			<div class="alert alert-success div-a">
				<p class="text-success"><?php echo($message); ?> 
					<a id="href" href="<?php echo($jumpUrl); ?>"><b id="wait"><?php echo($waitSecond); ?></b></p>
			</div>
		<else/>
			<div class="alert alert-danger div-a">
				<p class="error"><?php echo($error); ?>
				<a id="href" href="<?php echo($jumpUrl); ?>"></a><b id="wait"><?php echo($waitSecond); ?></b></p>
			</div>
		</present>
</div>
<script type="text/javascript">
(function(){
var wait = document.getElementById('wait'),href = document.getElementById('href').href;
var interval = setInterval(function(){
	var time = --wait.innerHTML;
	if(time == 0) {
		location.href = href;
		clearInterval(interval);
	};
}, 1000);
})();
</script>
</body>
</html>
