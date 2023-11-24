import registerColumnsBlock from "../scripts/registerColumnsBlock";

registerColumnsBlock(
    'icon', {
        parentTitle: 'Kartičky s ikonkami',
        childTitle: 'Kartička',
        // defaultBackgroundColor: 'light-brown',
        minColumnCount: 2,
        maxColumnCount: 4,
        supports: {
            color: false
        },
        template: [
            ['saleziani/icon', {color: '#f4524d', textColor: 'orange', lock: {move: true, remove: true}}],
            ['core/heading', {level: 3, placeholder: 'Nadpis', lock: {move: true, remove: true}}],
            ['core/paragraph', {
                lock: {move: true, remove: true},
                placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            }],
            ['core/paragraph', {content: '<a href="#" class="stretched-link">Čítať viac</a>', lock: {move: false, remove: false}}]
        ]
    });
