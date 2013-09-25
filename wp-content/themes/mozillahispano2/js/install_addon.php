<?php
	$id = str_replace('"', '', $_GET['id']);
	header('Content-type: application/javascript');
?>
	function install (aEvent)
	{
	  var params = {
		"<?=$id?>": { URL: aEvent.target.href,
				 IconURL: aEvent.target.getAttribute("iconURL"),
				 Hash: aEvent.target.getAttribute("hash"),
				 toString: function () { return this.URL; }
		}
	  };
	  InstallTrigger.install(params);

	  return false;
	}
