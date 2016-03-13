<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Hint&CxMvc</title>
<style type="text/css">
*{ padding: 0; margin: 0; }
body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
.system-message{ padding: 24px 48px; }
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-top: 10px}
.system-message .success{ line-height: 1.8em; font-size: 36px }
.copyright{ padding: 12px 48px; color: #999; }
.copyright a{ color: #000; text-decoration: none; }
</style>
</head>
<body>
<?php 
$hint = isset($hint)?$hint:'PAGE NOT FOUND!';
$message = isset($message)?$message:'Sorry, are working on it. Please check again later.';
?>
<div class="system-message">
<h1>:)</h1>
<p class="success"><?php echo($hint); ?></p>
<p class="jump"><?php echo($message); ?></p>
</div>
<div class="copyright">
<p><a title="官方网站" href="http://cxmvc.com" target="_blank">CxMvc</a><sup>2.1</sup> { Fast & Simple OOP PHP Framework } -- [ WE CAN DO IT JUST CXMVC ]</p>
</div>
</body>
</html>
