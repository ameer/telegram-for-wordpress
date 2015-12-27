<?php

/**
* m : Test Message
* c  : Test channel
* b : Test bot token
* 
*/
require_once("Notifcaster.class.php");
	switch ($_POST['subject']) {
		case 'm':
			//This will send a test message.
			$nt = new Notifcaster_Class();
			$nt->Notifcaster($_POST['api_token']);
			$result = $nt->notify($_POST['msg']);
			echo json_encode($result);
			break;
		case 'c':
			$nt = new Notifcaster_Class();
			$nt->_telegram($_POST['bot_token']);
			$result = $nt->channel_text($_POST['channel_username'], $_POST['msg']);
			echo json_encode($result);
			break;
		case 'b':
			$nt = new Notifcaster_Class();
			$nt->_telegram($_POST['bot_token']);
			$result = $nt->get_bot();
			echo json_encode($result);
			break;
		default:
			# code...
			break;
	}

	
?>
        
