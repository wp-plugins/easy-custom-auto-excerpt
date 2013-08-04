<?php

function tonjoo_ecae_load_default(&$options){
	if(!is_numeric($options['width'])){
		$options['width']=500;		
	}
	if($options['excerpt_method']==''){
		$options['excerpt_method']='word';		
	}
	if($options['show_image']==''){
		$options['show_image']='yes';		
	}
	if($options['home']==''){
		$options['home']='yes';		
	}
	if($options['home']==''){
		$options['home']='yes';		
	}
	if($options['search']==''){
		$options['search']='yes';		
	}
	if($options['archive']==''){
		$options['archive']='yes';		
	}
	if($options['justify']==''){
		$options['justify']='yes';		
	}
	if($options['read_more']==''){
		$options['read_more']='read more';		
	}

	
	if($options['show_image']==''){
		$options['show_image']='yes';		
	}

	return $options;
}