<!DOCTYPE html>
<html lang="ru">
  <head>
	<?php theme('head')?>
    </head>
    <body>
        <?php theme('before')?>
        <div class="container">
	        <div class="row">
				<div class="span12" id="header">
                                    <a href="<?php echo l();?>"><img src="<?php echo $theme->folder?>/img/logo.png"/></a>
					<?php theme('header')?>
				</div>
		    </div>
            <div class="row">
				<div class="span9" id="content">
					<?php theme('content')?>
				</div>
				<div class="span3" id="sidebar">
					<?php theme('sidebar')?>
				</div>
	        </div>
            <div class="row">
	            <div class="span12" id="footer">
		            <?php theme('footer')?>
	            </div>
	        </div>
        </div>
        <?php theme('after')?>
    </body>
</html>
