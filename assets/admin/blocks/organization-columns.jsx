import registerColumnsBlock from "../scripts/registerColumnsBlock";

registerColumnsBlock(
    'organization', {
        parentTitle: 'Kartičky organizácií',
        childTitle: 'Organizácia',
        defaultBackgroundColor: 'light-blue',
        minColumnCount: 3,
        maxColumnCount: 3,
        template: [
            ['core/image'],
            ['core/group', {className: 'content', metadata: {name: 'Obsah'}, lock: {move: true, remove: true}}, [
                ['core/group', {className: 'text', metadata: {name: 'Text'}, lock: {move: true, remove: true}}, [
                    ['core/heading', {level: 3, placeholder: 'Názov organizácie', lock: {move: true, remove: true}}],
                    ['core/paragraph', {
                        className: 'text',
                        lock: {move: true, remove: true},
                        placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                    }],
                ]],
                ['core/paragraph', {className: 'link', content: '<a href="#" class="stretched-link">Čítať viac</a>'}]
            ]]
        ]
    });
