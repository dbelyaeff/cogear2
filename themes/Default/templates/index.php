<!DOCTYPE html>
<html lang="ru">
  <head>
	<?=theme('head')?>
    </head>
    <body>
        <?=theme('before')?>
        <div class="container">
	        <div class="row">
				<div class="span16" id="header">
					<?=theme('header')?>
				</div>
		    </div>
            <div class="row">
				<div class="span3 columns" id="sidebar">
					<?=theme('sidebar')?>
				</div>
				<div class="span13 columns" id="content">
					<?=theme('content')?>
				</div>
	        </div>
            <div class="row">
	            <div class="span16" id="footer">
		            <?=theme('footer')?>
	            </div>
	        </div>
        </div>
        <?=theme('after')?>
    </body>
</html>
