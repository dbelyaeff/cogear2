<?php 
/**
 * Пример кода использования авторизации через Loginza API
 */
header ('Content-type: text/html; charset=utf-8');

session_start();

require_once 'libs/LoginzaAPI.class.php';
require_once 'libs/LoginzaUserProfile.class.php';

// объект работы с Loginza API
$LoginzaAPI = new LoginzaAPI();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Пример авторизации через Loginza API</title>

<!-- Insert Loginza Widget JavaScript Code -->
<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>

</head>
<body>
<?php 
// проверка переданного токена
if (!empty($_POST['token'])) {
	// получаем профиль авторизованного пользователя
	$UserProfile = $LoginzaAPI->getAuthInfo($_POST['token']);
	
	// проверка на ошибки
	if (!empty($UserProfile->error_type)) {
		// есть ошибки, выводим их
		// в рабочем примере данные ошибки не следует выводить пользователю, так как они несут информационный характер только для разработчика
		echo $UserProfile->error_type.": ".$UserProfile->error_message;
	} elseif (empty($UserProfile)) {
		// прочие ошибки
		echo 'Temporary error.';
	} else {
		// ошибок нет запоминаем пользователя как авторизованного
		$_SESSION['loginza']['is_auth'] = 1;
		// запоминаем профиль пользователя в сессию или создаем локальную учетную запись пользователя в БД
		$_SESSION['loginza']['profile'] = $UserProfile;
	}
} elseif (isset($_GET['quit'])) {
	// выход пользователя
	unset($_SESSION['loginza']);
}

// проверка авторизации, вывод профиля если пользователь авторизован ранее
if (!empty($_SESSION['loginza']['is_auth'])) {
	
	// объект генерации недостаюих полей (если требуется)
	$LoginzaProfile = new LoginzaUserProfile($_SESSION['loginza']['profile']);
	
	// пользователь уже прошел авторизацию
	$avatar = '';
	if (!empty($_SESSION['loginza']['profile']->photo)) {
		$avatar = '<img src="'.$_SESSION['loginza']['profile']->photo.'" height="30" align="top"/> ';
	}
	echo "<h3>Приветствуем Вас:</h3>";
	echo $avatar . $LoginzaProfile->genDisplayName().', <a href="?quit">Выход ('.$LoginzaProfile->genNickname().')</a>';
	
	// вывод данных полученных через LoginzaUserProfile
	echo "<p>";
	echo "Ник: ".$LoginzaProfile->genNickname()."<br/>";
	echo "Отображать как: ".$LoginzaProfile->genDisplayName()."<br/>";
	echo "Полное имя: ".$LoginzaProfile->genFullName()."<br/>";
	echo "Сайт: ".$LoginzaProfile->genUserSite()."<br/>";
	echo "</p>";
	
	// выводим переданные данные от Loginza API
	$LoginzaAPI->debugPrint($_SESSION['loginza']['profile']);
} else {
	// требуетс авторизация, вывод ссылки на Loginza виджет
	echo "<h3>Блок авторизации:</h3>";
	echo '<a href="'.$LoginzaAPI->getWidgetUrl().'" class="loginza">Для авторизации нажмите ссылку</a>';
}
?>
</body>
</html>