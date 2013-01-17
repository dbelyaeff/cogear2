<!DOCTYPE html>
<html lang="ru">
    <head>
        <?php echo theme('head') ?>
    </head>
    <body id="login">
        <div class="container">
            <div class="row">
                <div class="span7" id="wrapper">
                    <a href="http://cogear.ru"><img src="<?php echo l('/themes/Default/img/logo.png')?>" alt="Cogear"></a>
                    <div id="content">
                        <?php echo theme('info') ?>
                        <?php echo theme('content') ?>
                    </div>
                    <script>
                        $(document).ready(function(){
                            $('input').first().focus();
                        })
                    </script>
                </div>
            </div>
        </div>
        <?php echo theme('after') ?>
    </body>
</html>
