<?php defined('IN_IA') or exit('Access Denied');?>			</div>
		</div>
	</div>
	<script>
		function subscribe(){
			$.post("<?php  echo url('utility/subscribe');?>", function(){
				setTimeout(subscribe, 5000);
			});
		}
		function sync() {
			$.post("<?php  echo url('utility/sync');?>", function(){
				setTimeout(sync, 60000);
			});
		}
		function tomedia(src) {
			if(typeof src != 'string')
				return '';
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
		/*$(function(){
			subscribe();
			sync();
		});*/
		<?php  if($_W['uid']) { ?>
			function checknotice() {
				$.post("<?php  echo url('utility/notice')?>", {}, function(data){
					var data = $.parseJSON(data);
					$('#notice-container').html(data.notices);
					$('#notice-total').html(data.total);
					if(data.total > 0) {
						$('#notice-total').css('background', '#ff9900');
					} else {
						$('#notice-total').css('background', '');
					}
					setTimeout(checknotice, 60000);
				});
			}
			checknotice();
		<?php  } ?>
		//global hook
		<?php  if($_W['uid']) { ?>
			$.ajax({
				type: 'get',
				url: "<?php  echo $_W['siteroot'].'web/'.$this->createWebUrl('hook')?>",
				success:function(){}
			});
		<?php  } ?>
	</script>
<?php (!empty($this) && $this instanceof WeModuleSite || 1) ? (include $this->template('footer-base', TEMPLATE_INCLUDEPATH)) : (include template('footer-base', TEMPLATE_INCLUDEPATH));?>
