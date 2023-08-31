(wp => {
    const template = [
        ['core/heading', {content: 'Nadpis výzvy k akcii', level: 2}],
        ['core/paragraph', {content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque at justo viverra metus egestas hendrerit. In sed mauris eget mi vestibulum interdum sit amet ut magna. Sed eu molestie mi. Cras malesuada ut neque vel mollis. Pellentesque sollicitudin quis sapien eu tincidunt. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce metus quam, elementum vitae accumsan tristique, consequat non turpis.'}],
        ['core/button', {text: 'Vestibulum ante ipsum'}]
    ];

    return wp.blocks.registerBlockType('saleziani/call-to-action', {
        category: 'theme',
        description: 'Vytvorte pútavý blok s nadpisom, popisom a tlačidlom.',
        example: {innerBlocks: template.map((i) => ({name: i[0], attributes: i[1]}))},
        icon: 'superhero-alt',
        supports: {
            align: true,
            color: {
                background: true,
                text: false
            },
            customClassName: false
        },
        title: 'Výzva k akcii',
        edit: props => wp.element.createElement(
            'div',
            wp.blockEditor.useBlockProps({className: props.className}),
            wp.element.createElement(
                'div',
                {className: 'inner'},
                wp.element.createElement(
                    wp.blockEditor.InnerBlocks,
                    {
                        allowedBlocks: ['core/button', 'core/heading', 'core/paragraph'],
                        template: template,
                        templateLock: 'all'
                    }
                )
            )
        ),
        save: () => wp.element.createElement(
            'div',
            {},
            wp.element.createElement(
                'div',
                {className: 'inner'},
                wp.element.createElement(wp.blockEditor.InnerBlocks.Content)
            )
        )
    });
})(window.wp);
