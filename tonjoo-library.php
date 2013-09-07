<?php

if ( ! function_exists( 'tj_print_select_option' ) ) :
function tj_print_select_option($options){
	$p='';
	$r='';

	foreach ( $options['select_array'] as $select ) {
		$label = $select['label'];

		if ( $options['value'] == $select['value'] ) // Make default first in list
			$p = "<option selected='selected' value='" . esc_attr( $select['value'] ) . "'>$label</option>";
		else
			$r .= "<option value='" . esc_attr( $select['value'] ) . "'>$label</option>";
	}
	



	$print_select= "<tr valign='top' id='{$options['id']}'>
						<th scope='row'>{$options['label']}</th>
						<td>
							<select name='{$options['name']}'>
							{$p}{$r}
							</select>
							<label class='description' >{$options['description']}</label>
						</td>
					</tr>
					";

	echo $print_select;
}

endif;

if ( ! function_exists( 'tj_print_text_option' ) ) :
function tj_print_text_option($options){




	$print_select= "<tr valign='top' id='{$options[id]}'>
						<th scope='row'>{$options[label]}</th>
						<td>
							<input type='text' name='{$options[name]}' value='{$options['value']}'>		
						</td>
					</tr>
					";

	echo $print_select;
}

endif;

if ( ! function_exists( 'tj_print_text_area_option' ) ) :
function tj_print_text_area_option($options){


	if(!$options['row'])
		$options['row']=4;
	if(!$options['column'])
		$options['column']=50;


	

	$print_select= "<tr valign='top' id='{$options[id]}' >
						<th scope='row'>{$options[label]}</th>
						<td>
							<textarea  name='{$options[name]}' rows='{$options['row']}' cols='{$options['column']}'>{$options['value']}</textarea>		
						</td>
					</tr>
					";

	echo $print_select;
}

endif;