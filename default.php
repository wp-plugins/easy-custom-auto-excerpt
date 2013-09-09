<?php

function tonjoo_ecae_load_default(&$options){
	if(!is_numeric($options['width'])){
		$options['width']=500;		
	}
	if(!isset($options['excerpt_method'])){
		$options['excerpt_method']='word';		
	}
	if(!isset($options['show_image'])){
		$options['show_image']='yes';		
	}
	if(!isset($options['home'])){
		$options['home']='yes';		
	}
	if(!isset($options['home'])){
		$options['home']='yes';		
	}
	if(!isset($options['search'])){
		$options['search']='yes';		
	}
	if(!isset($options['archive'])){
		$options['archive']='yes';		
	}
	if(!isset($options['justify'])){
		$options['justify']='no';		
	}
	if(!isset($options['read_more'])){
		$options['read_more']='read more';		
	}

	
	if(!isset($options['show_image'])){
		$options['show_image']='yes';		
	}

	return $options;
}