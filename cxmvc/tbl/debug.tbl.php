<!DOCTYPE HTML>
<html>
	<head>
		<title>Debug&CxMvc</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<style type="text/css">
		*{padding: 0px;margin: 0px;}
		body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 14px; }
		h2{margin-bottom: 5px;padding-top: 10px;}
		table{border-collapse:collapse;}
		td{border-right: 1px solid #CCC;border-bottom: 1px solid #CCC;background: #EEEFFF;padding: 2px;min-width: 50px;}
		.breakall{word-break:break-all;}
		.nav{width:auto;height: auto;position: fixed!important;position: absolute;top:0px!important;top:0px;right:0px;padding-right:0px;
		top: expression(eval(document.compatMode && document.compatMode=='CSS1Compat') ? documentElement.scrollTop+(documentElement.clientHeight - this.clientHeight):document.body.scrollTop+(document.body.clientHeight - this.clientHeight));
		}
		.nav ul{list-style: none;}
		.nav ul li{padding:5px; background: #ccc;border-bottom: 1px solid #AAA;padding-right:10px}
 		.nav ul li:hover{background: #BBB;}
		.nav a{text-decoration: none;}
		.copyright{ padding: 12px 0px; color: #999; }
		.copyright a{ color: #000; text-decoration: none; }
		</style>
	</head>
	<body>
	<?php
		foreach($result as $k => $v) {
			extract($v);
	 		if(!empty($e)){
				$color = '#FF4040';
			}
			else{
				$color = '#FFD39B';
			}
		?>
		<div style="background:<?=$color?>;padding-left: 5px;padding-bottom: 5px;" id="debug<?=$k?>">
			<h2>Time:<?=$startTime ?></h2>
			<div>Url : <?=$url ?></div>
			<div>Spend: <?=$endTime?>s &nbsp;&nbsp;Mem: <?=$mem?></div>
		</div>
		<?php if(!empty($log)) {?>
		<h2>Log</h2>
		<div>
		<table>
			<?php foreach ($log as $v ){ ?>
				<tr><td><?=is_array($v['k'])?e($v['k']):$v['k'] ?></td><td class="breakall"><?=is_array($v['v'])?e($v['v']):$v['v'] ?></td></tr>
			<?php }?>
		</table>
		</div>
		<?php }?>
		
		<?php if(!empty($get)) {?>
		<h2>Get</h2>
		<div>
		<table>
			<?php foreach ($get as $k => $v ){ ?>
				<tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?e($v):$v ?></td></tr>
			<?php }?>
		</table>
		</div>
		<?php }?>
		
		<?php if(!empty($post)) {?>
		<h2>Post</h2>
		<div>
		<table>
			<?php foreach ($post as $k => $v ){ ?>
				<tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?e($v):$v ?></td></tr>
			<?php }?>
		</table>
		</div>
		<?php }?>
		
		<?php if(!empty($e)){?>
		<h2>Error</h2>
		<div>
		<table>
			<tr><td width="80px">错误级别:</td><td class="breakall"><?=$e['type']?></td></tr>
			<tr><td>错误信息:</td><td class="breakall"><?=$e['message']?></td></tr>
			<tr><td>错误行号:</td><td class="breakall"><?=$e['line']?></td></tr>
			<tr><td>错误文件:</td><td class="breakall"><?=$e['file']?></td></tr>
		</table>
		</div>
		<?php }?>
		
		<?php if(!empty($sql)){?>
		<h2>Sql</h2>
		<div>
		<table>
			<?php foreach ($sql as $v){?>
				<tr><td colspan="2"><?=$v['sql'] ?></td></tr>
				<?php 
					$i = 1;
					if(!empty($v['param'])) 
					foreach ($v['param'] as $v){
				?>
				<tr><td width="10px;"><?=$i++; ?></td><td class="breakall"><?=$v ?></td></tr>
			<?php }}?>
		</table>
		</div>
		<?php }?>

			<?php if(!empty($session)) {?>
				<h2>Session</h2>
				<div>
					<table>
						<?php foreach ($session as $k => $v ){ ?>
							<tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?e($v):$v ?></td></tr>
						<?php }?>
					</table>
				</div>
			<?php }?>

			<?php if(!empty($cookie)) {?>
				<h2>Cookie</h2>
				<div>
					<table>
						<?php foreach ($cookie as $k => $v ){ ?>
							<tr><td><?=$k ?></td><td class="breakall"><?=is_array($v)?e($v):$v ?></td></tr>
						<?php }?>
					</table>
				</div>
			<?php }?>

			<?php if(!empty($server)){?>
				<h2>Server</h2>
				<div>
					<table>
						<?php foreach ($server as $k => $v ){ ?>
							<tr ><td><?=$k ?></td><td class="breakall"><?=is_array($v)?e($v):$v ?></td></tr>
						<?php }?>
					</table>
				</div>
			<?php }?>

			<?php if(!empty($runfile)){?>
				<h2>Trace</h2>
				<div>
					<table>
						<?php foreach ($runfile as  $v ){ ?>
							<tr><td><?=$v ?></td></tr>
						<?php }?>
					</table>
				</div>
			<?php }?>

		<div style="height: 20px;"></div>
	<?php }?>
		<div class="nav">
			<ul>
				<li><a href="<?=URL?>debug/">currently</a></li>
				<?php foreach ($result as $k=>$v){?>
					<li><a href="#debug<?=$k?>" title="<?=$v['url'] ?>"><?php echo date("H:i:s",strtotime($v['startTime']))?></a></li>
				<?php }?>
			</ul>
		</div>
		<div class="copyright">
		<p><a title="官方网站" href="http://cxmvc.com" target="_blank">CxMvc</a><sup>2.1</sup> { Fast & Simple OOP PHP Framework } -- [ WE CAN DO IT JUST CXMVC ]</p>
		</div>
	</body>
</html>












