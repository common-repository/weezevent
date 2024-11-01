/* This section of the code registers a new block, sets an icon and a category, and indicates what type of fields it'll include. */
wp.blocks.registerBlockType('weezevent/weezevent-block', {
  title: wp.i18n.__( 'Weezevent Widget', 'weezevent'), 
  icon: 'tickets-alt',
  category: 'common',
  attributes: {
    code: {type: 'string'},
    multi: {type: 'boolean'},
  },
  keywords: [ 
      wp.i18n.__( 'Ticket', 'weezevent'), 
      wp.i18n.__( 'Ticketing', 'weezevent'), 
      wp.i18n.__( 'Event', 'weezevent'), 
    ],
  
/* This configures how the content and color fields will work, and sets up the necessary elements */
  edit: function( props ) {
        function updateMulti() {
          props.setAttributes({multi: !props.attributes.multi});
        }
        
        function updateData(value) {
            const doc = new DOMParser().parseFromString(value, "text/html");
            const links = doc.querySelectorAll("a");

            if (links && links.length > 0){
                props.setAttributes({ code: links[0].outerHTML });
            }
        }
        
		return [
			wp.element.createElement( wp.components.ServerSideRender, {
				block: 'weezevent/weezevent-block',
				attributes: props.attributes,
			} ),
			
			wp.element.createElement( wp.editor.InspectorControls, {},
              wp.element.createElement(wp.components.PanelBody, {title: 'Settings', initialOpen: true},			
			wp.element.createElement( wp.components.TextareaControl, {
				label: wp.i18n.__( 'Widget code', 'weezevent'),
				value: props.attributes.code,
				onChange: updateData,
                placeholder: '<a href="...">'
			} ),
			wp.element.createElement( wp.components.ToggleControl, {
				label: wp.i18n.__( 'Multiple events sidget', 'weezevent'),
				checked: props.attributes.multi,
				onChange: updateMulti
			} )
    	 ),
         wp.element.createElement(wp.components.PanelBody, {title: wp.i18n.__( 'Help', 'weezevent'), initialOpen: false},
               wp.element.createElement('p', {},wp.i18n.__('You can also use the shortcode [weezevent] to display the widget module with all the data specified in the admin panel.', 'weezevent'))
         )
			),
		];
	},  
  save: function(props) {
    return null;
  }
});