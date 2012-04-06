<!DOCTYPE html>
<html lang="ru">
  <head>
	<?php theme('head')?>
    </head>
    <body>
        <?php theme('before')?>
        <div class="container">
	        <div class="row">
				<div class="span16" id="header">
					<?php theme('header')?>
				</div>
		    </div>
            <div class="row">
				<div class="span3 columns" id="sidebar">
					<?php theme('sidebar')?>
				</div>
				<div class="span13 columns" id="content">
					<?php theme('content')?>
				</div>
	        </div>
            <div class="row">
	            <div class="span16" id="footer">
		            <?php theme('footer')?>
	            </div>
	        </div>
        </div>
        <?php theme('after')?>
    </body>
</html>
