(wp => {
    return wp.blocks.registerBlockType('saleziani/newsletter-form', {
        attributes: {
            layout: {
                default: 'two-columns-with-background',
                type: 'string'
            }
        },
        category: 'theme',
        description: 'Získajte nových adresátov vašich newslettrov.',
        example: {},
        icon: 'email',
        title: 'Newsletter formulár',
        edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(), [
                wp.element.createElement(wp.blockEditor.InspectorControls, {key: 'inspector-controls'},
                    wp.element.createElement(wp.components.PanelBody, {title: 'Vzhľad'},
                        wp.element.createElement(wp.components.SelectControl, {
                            label: 'Rozloženie',
                            onChange: (value) => props.setAttributes({layout: value}),
                            options: [
                                {label: 'Dva stĺpce s pozadím (pätička)', value: 'two-columns-with-background'},
                                {label: 'Jeden stĺpec bez pozadia (obsah)', value: 'single-column-no-background'}
                            ],
                            value: props.attributes.layout
                        })
                    )
                ),
                wp.element.createElement('div', {className: 'wp-block-saleziani-newsletter-form', key: 'preview'}, 'Tu sa zobrazí newsletter formulár.')
            ]
        )
    });

})(window.wp);
