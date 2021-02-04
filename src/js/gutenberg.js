var edited = false;

function createBlock(blockConfig) {
	const { registerBlockType } = wp.blocks;
	const { createElement } = wp.element;
	const { InspectorControls }  = wp.blockEditor;
	const { CheckboxControl, RadioControl, SelectControl, TextControl, TextareaControl, ToggleControl } = wp.components;
	const { serverSideRender } = wp;
    
    registerBlockType( blockConfig.block.blocktype, {
		title: blockConfig.block.title,
		category: blockConfig.block.category,
		icon: blockConfig.block.icon,
		construct(){
			props.setAttributes({ countit: 0 });
		},
		edit( props ){
			const att = props.attributes;
			const setAtts = props.setAttributes;

			function changeField( val ){
				if ( blockConfig[this]['type'] == 'number' ){
					val = parseInt( val );
				}
				setAtts( {[this]: val} );
			}	
		
			function clean(obj) {
				for (var propName in obj) {
					if (obj[propName] === null || obj[propName] === undefined) {
						delete obj[propName];
				  	}
				}
			}
	
			if ( ( props['isSelected'] === false ) && ( edited === true ) ){
				clean( att );
				return createElement( serverSideRender, { block: blockConfig.block.blocktype, attributes: att });
			} else {
				var ret = [];
				ret.push( createElement( 'div', { className: 'components-placeholder__label' }, [ createElement( 'span', { className: 'editor-block-icon block-editor-block-icon dashicons-before dashicons-' + blockConfig.block.icon }, null ), blockConfig.block.title ] ) );

				for ( var fieldname in blockConfig ){
					switch( blockConfig[fieldname]['field_type'] ){
						case 'checkbox': 
							ret.push( createElement( CheckboxControl, { checked: ( typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname]['default'] ), label: blockConfig[fieldname]['label'], onChange: changeField.bind( fieldname ) } ) );
							break;
						case 'radio': 
							var opts = [];
							for ( var v in blockConfig[fieldname]['values'] ){
								opts.push( JSON.parse( '{"value":"' + v + '", "label":"' + blockConfig[fieldname]['values'][v] + '"}' ) );
							}
							ret.push( createElement( RadioControl, { selected: ( typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname]['default'] ), label: blockConfig[fieldname]['label'], onChange: changeField.bind( fieldname ), options: opts } ) );
							break;
						case 'multi_select': 
						case 'select': 
                            var opts = [];
                            for (var i = 0; i < blockConfig[fieldname]['values'].length; i++) { 
                                opts.push( JSON.parse( '{"value":"' + blockConfig[fieldname]['values'][i]['id'] + '", "label":"' + blockConfig[fieldname]['values'][i]['val'] + '"}' ) );
							}
							ret.push( createElement( SelectControl, { multiple: ( blockConfig[fieldname]['field_type'] == 'multi_select' ? 1 : 0 ), value: att[fieldname], label: blockConfig[fieldname]['label'], type: blockConfig[fieldname]['type'], onChange: changeField.bind( fieldname ), options: opts } ) );
							break;
						case 'text': 
							ret.push( createElement( TextControl, { value: att[fieldname], label: blockConfig[fieldname]['label'], type: blockConfig[fieldname]['type'], onChange: changeField.bind( fieldname ) } ) );
							break;
						case 'textarea': 
							ret.push( createElement( TextareaControl, { value: att[fieldname], label: blockConfig[fieldname]['label'], type: blockConfig[fieldname]['type'], onChange: changeField.bind( fieldname ) } ) );
							break;
						case 'toggle': 
							ret.push( createElement( ToggleControl, { checked: ( typeof att[fieldname] !== 'undefined' ? att[fieldname] : blockConfig[fieldname]['default'] ), label: blockConfig[fieldname]['label'], type: blockConfig[fieldname]['type'], onChange: changeField.bind( fieldname ) } ) );
							break;
					}
				}

				edited = true;

			    return createElement('div', { className: "components-placeholder" }, ret )
			}
		},
		save( props ){
			return null;
		}
	} );
}