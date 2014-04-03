{if $error == ''}
	{if $page->site->sabintegrationtype > 0}
		<p style="text-align:center;">
			The following queue is pulled from <a href="{$sabserver|escape:"htmlall"}">{$sabserver|escape:"htmlall"}</a>.
			<br />
			{if $page->site->sabintegrationtype == 2}Edit your queue settings in <a href="{$smarty.const.WWW_TOP}/profileedit">your profile</a>.{/if}
		</p>
		<div class="sab_queue"></div>
		{literal}
			<script type="text/javascript">
				function getQueue()
				{
					var rand_no = Math.random();
					$.ajax({
						url: "queuedata?id=" + rand_no,
						cache: false,
						success: function(html)
						{
							$(".sab_queue").html(html);
							setTimeout("getQueue()", 2500);
						},
						error: function ()
						{
							$(".sab_queue").html("<p style='text-align:center;'>Could not contact your queue. <a href=\"javascript:location.reload(true)\">Refresh</a></p>");
						},
						timeout:5000
					});
				}
			</script>
		{/literal}
		<body onLoad="getQueue();">
	{else}
		<p style="text-align:center;">The {$queueType} queue has been disabled by the administrator.</p>
	{/if}
{else}
	<p style="text-align:center;">{$error}</p>
{/if}