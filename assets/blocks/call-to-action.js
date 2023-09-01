(wp => {
    const template = [
        ['core/heading', {content: 'Vaša podpora je dôležitá', level: 2}],
        ['core/paragraph', {content: 'Saleziánske dielo je sieť saleziánskych komunitných centier, v ktorých tisíce ľudí denne trávia zmysluplný čas. Každý je vítaný. Tvoj pravidelný mesačný príspevok je potrebný pre udržanie a rozvoj športových, kultúrnych, sociálnych a duchovných aktivít pre deti, mladých, rodičov aj seniorov.'}],
        ['core/button', {text: 'Podporiť teraz'}]
    ];

    return wp.blocks.registerBlockType('saleziani/call-to-action', {
        attributes: {
            align: {
                default: 'full',
                type: 'string'
            },
            backgroundColor: {
                default: 'apricot',
                type: 'string'
            }
        },
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
