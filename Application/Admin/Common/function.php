<?php
	function get_agent_level($l){
		$levels = C('AGENT_LEVELS');
		foreach($levels as $level){
			if($level['value'] == $l){
				return $level['name'];
			}
		}
		return '其他';
	}
?>