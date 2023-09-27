(wp => wp.blocks.registerBlockType('saleziani/latest-posts', {
    attributes: {
        count: {
            default: 3,
            type: 'integer'
        }
    },
    category: 'theme',
    description: 'Zobrazte tri najnovšie články.',
    example: {},
    icon: 'text-page',
    title: 'Najnovšie články',
    edit: props => wp.element.createElement('div', wp.blockEditor.useBlockProps(), [
            wp.element.createElement(wp.blockEditor.InspectorControls, {key: 'inspector-controls'},
                wp.element.createElement(wp.components.PanelBody, {key: 'target-page', title: 'Nastavenia'},
                    wp.element.createElement(wp.components.RangeControl, {
                        label: 'Počet článkov',
                        max: 12,
                        min: 1,
                        onChange: (value) => props.setAttributes({count: value}),
                        value: props.attributes.count
                    })
                )
            ),
            wp.element.createElement(wp.serverSideRender, {attributes: props.attributes, block: props.name, key: 'preview'})
        ]
    )
}))(window.wp);
