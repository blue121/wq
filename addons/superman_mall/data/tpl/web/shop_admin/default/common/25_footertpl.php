<?php defined('IN_IA') or exit('Access Denied');?>			</div>
		</div>
	</div>
	<script>
		function tomedia(src) {
			if(typeof src != 'string'){
				return '';
			}
			if(src.indexOf('http://') == 0 || src.indexOf('https://') == 0) {
				return src;
			} else if(src.indexOf('./addons') == 0) {
				src=src.substr(2);
				return window.sysinfo.siteroot + src;
			} else if(src.indexOf('../addons') == 0 || src.indexOf('../attachment') == 0) {
				src=src.substr(3);
				return window.sysinfo.siteroot + src;
			} else if(src.indexOf('./resource') == 0) {
				src=src.substr(2);
				return window.sysinfo.siteroot + 'app/' + src;
			} else if(src.indexOf('images/') == 0) {
				return window.sysinfo.attachurl+ src;
			}
		}
		//global hook
		<?php  if($_W['uid']) { ?>
			$.ajax({
				type: 'get',
				url: "<?php  echo $_W['siteroot'].'web/'.$this->createWebUrl('hook')?>",
				success:function(){}
			});
		<?php  } ?>
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('common/footer-base', TEMPLATE_INCLUDEPATH)) : (include template('common/footer-base', TEMPLATE_INCLUDEPATH));?>
