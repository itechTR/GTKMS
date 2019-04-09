<?php 

// Değişken bilgileri
$thisscript = "gtkms.php";
$thisurl = "http://sms.ara.la/".$thisscript; // Bunu, sunucunuzda bu komut dosyasının çalıştığı şekilde değiştirin
$uuid = uniqid(); // Benzersiz mesaj tanımlayıcısını üret
$messageid = Null; // Mesaj kimliğini GET isteğinden tut
$message = Null; // Asıl mesajın kendisi
$viewed = Null; // 0=görüntülenmedi, 1=görüntülendi
$ipaddress = Null; // Mesajı görüntülemek için kullanılan bilgisayarın IP adresi
$timestamp = Null; // Mesajın ne zaman görüntülendiğinin zaman damgası

// Connect to MySQL database
$con = mysqli_connect('localhost','gtkmsdbusers','gtkmsdbusers_pass','gtkmsdb'); // MySQL bağlantı bilgilerini buradan değiştirin
if (!$con) {
	die('Could not connect: ' . mysqli_error());
}
mysqli_select_db("gtkmsdb", $con); // veritabanı tablosuna bağlan

// IP adresini almak için özel işlev
function get_ip_address() {
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
		if (array_key_exists($key, $_SERVER) === true) {
			foreach (explode(',', $_SERVER[$key]) as $ip) {
				if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
					return $ip;
				}
			}
		}
	}
}

// GET isteğinde iletilen mesaj kimliğine göre mesajı veritabanından al
if (isset($_GET['messageid'])) {
	$messageid = $_GET['messageid'];
	if (strlen($messageid) != 13) {
		echo('Geçersiz mesaj kimliği. 13 karakter olmalı');
		mysqli_close($con);
		exit;
	}
	$result = mysqli_query($con, "select * from messages where id='".$messageid."'");
	while ($row = mysqli_fetch_array($result)) {
		if ($row['viewed'] == 0) {
			$message = $row['message'];
			// bir kez okunduğunda mesajı sıfırla
			mysqli_query($con, "update messages set viewed=1, timestamp='".date(DATE_RFC822)."', ipaddress='".get_ip_address()."', message='0' where id='".$messageid."'");
		}
		else {
			$ipaddress = $row['ipaddress'];
			$timestamp = $row['timestamp'];
		}
	}
}

// Formdan gönderilen mesajı kaydet
if (isset($_POST['msg'])) {
	if (base64_decode($_POST['msg'], true)) {
		mysqli_query($con, "insert into messages (id, message, viewed) values ('".$uuid."','".$_POST['msg']."','0')");
	}
	else {
		echo "Gönderiyle ilgili bir sorun vardı.";
		mysqli_close($con);
		exit;
	}
}

// MySQL bağlantısını temizle
mysqli_close($con);
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width">
	<meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@itechspirit">
    <meta name="twitter:creator" content="@itechspirit">
    <meta name="twitter:url" content="http://sms.ara.la/">
    <meta name="viewport" content="width=device-width, initial-scale=0.55"/>
	<title>Güvenli ve Tek Kullanımlık Mesaj Sistemi</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script>
	function base64_decode(a){var b="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var c,d,e,f,g,h,i,j,k=0,l=0,m="",n=[];if(!a){return a}a+="";do{f=b.indexOf(a.charAt(k++));g=b.indexOf(a.charAt(k++));h=b.indexOf(a.charAt(k++));i=b.indexOf(a.charAt(k++));j=f<<18|g<<12|h<<6|i;c=j>>16&255;d=j>>8&255;e=j&255;if(h==64){n[l++]=String.fromCharCode(c)}else if(i==64){n[l++]=String.fromCharCode(c,d)}else{n[l++]=String.fromCharCode(c,d,e)}}while(k<a.length);m=n.join("");return m}function base64_encode(a){var b="ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";var c,d,e,f,g,h,i,j,k=0,l=0,m="",n=[];if(!a){return a}do{c=a.charCodeAt(k++);d=a.charCodeAt(k++);e=a.charCodeAt(k++);j=c<<16|d<<8|e;f=j>>18&63;g=j>>12&63;h=j>>6&63;i=j&63;n[l++]=b.charAt(f)+b.charAt(g)+b.charAt(h)+b.charAt(i)}while(k<a.length);m=n.join("");var o=a.length%3;return(o?m.slice(0,o-3):m)+"===".slice(o||3)}
	$(function(){
		<?php if (isset($messageid)) { ?>
			<?php if (isset($message)) { ?>
				var msg = "<?php echo $message; ?>";
				var msg_decoded = base64_decode(msg);
				var sentences = msg_decoded.match(/[^\.!\?]+[\.!\?]+/g);
				if (!sentences){
					sentences = [msg_decoded];
				}
				$("#play").click(function(e){
					e.preventDefault();
					var queue = $.Deferred();
					queue.resolve();
					$.each(sentences, function(i, sentence){
						queue = queue.pipe(function(){
							return $("#playmsg").show().html(sentence).fadeOut(5000);
						});
					});
				});
			<?php } ?>
		<?php } else { ?>
		$("#secureform").submit(function(e){
			if ($("#msg").val() != "") {
				$("#msg").val(base64_encode($("#msg").val()));
			} else {
				alert("Mesaj boş! Lütfen mesajınızı giriniz.");
				$("#msg").focus();
				return false;
			}
		});
		<?php } ?>
	});
	</script>
	<style>
	body {
		background: #fff;
		font-family: Georgia, serif;
		font-size: 0.75em;
	}
	h1, h2, h3 {
		font-family: Roboto, sans-serif;
	}
	label {
		font-family: Roboto, sans-serif;
		font-weight: bold;
	}
	#container {
		max-width: 800px;
		margin: 0 auto;
		padding: 1em;
	}
	#msg {
		width: 100%;
		background: #f0ffff;
	}
	#playmsg {
		margin: 1em 0;
		font-size: 1.2em;
		background: #f0ffff;
	}
	.highlight {
		background: yellow;
	}
	input[type=submit] {
		margin-top: 1em;
		padding: 0.8em;
		background: #000;
		color: #fff;
	}
	</style>
</head>
<body>
<div id="container">
	<h1>GTKMS</h1>
	<h3>Güvenli Tek Kullanımlık Mesaj Sistemi</h3>
	<hr>
	<?php if (isset($_POST['msg'])) { ?>
		<p>Bu URL'yi kopyalayıp yapıştırın.</p>
		<span class="highlight"><?php echo $thisurl; ?>?messageid=<?php echo $uuid; ?></span>
		<p><b>Not:</b> Tarayıcınızda bu URL'ye gitmeyin, aksi takdirde mesaj alıcınız tarafından görüntülenemez.</p>
		<p>Başka bir tek kullanımlık mesaj oluşturmak için  <a href="<?php echo $thisscript; ?>">tıkla</a>.</p>
	<?php } else { ?>
		<p> 
		<?php if (isset($messageid)) { ?>
			<b>Not:</b> Bu mesajın içeriği yalnızca bir kez görüntülenebilir. Buraya geldiyseniz ve mesaj görüntülenemiyorsa, bu mesaj zaten okunmuştur. Mesaj okunduğunda içeriği sunucuda yok edilir.
		<?php } else { ?>
			<b>Not:</b> Yalnızca alıcı tarafından bir kez okunabilen güvenli bir tek kullanımlık mesaj oluşturmak için bu formu kullanın. Mesajınızı oluşturmayı tamamladığınızda, istediğiniz alıcıya gönderebileceğiniz benzersiz bir URL oluşturmak için düğmeyi tıklayın. Mesajın içeriği, sunucu alınır alınmaz sunucuda imha edilir.
		<?php } ?>
		</p>
		<?php if (isset($timestamp) && isset($ipaddress)) { ?>
			<p class="highlight">Bu mesaj <?php echo $timestamp; ?> tarihinde ve şu <?php echo $ipaddress; ?>ip adresi tarafından okundu.</p>
		<?php } ?>
		<form id="secureform" action="<?php echo $thisscript; ?>" method="post">
			<label for="msg">Mesajınız:</label>
			<?php if (isset($messageid)) { ?>
				<?php if (isset($message)) { ?>
					<button id="play">Mesajı göster</button>
					<div id="playmsg"></div>
				<?php } ?>
				<p>Kendi tek kullanımlık mesajınızı oluşturmak için <a href="<?php echo $thisscript; ?>">tıkla</a>.</p>
			<?php } else { ?>
				<textarea id="msg" name="msg" rows="15"></textarea>
				<input type="submit" value="Mesajı kaydet ve bağlantı oluştur">
			<?php } ?>
		</form>
	<?php } ?>
</div>
</body>
</html>
