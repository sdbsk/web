import registerColumnsBlock from "../scripts/registerColumnsBlock";

registerColumnsBlock(
    'project', {
        parentTitle: 'Kartičky projektov',
        childTitle: 'Projekt',
        defaultBackgroundColor: 'yellow',
        minColumnCount: 2,
        maxColumnCount: 2,
        template: [
            ['core/image'],
            ['core/group', {className: 'content', metadata: {name: 'Obsah'}, lock: {move: true, remove: true}}, [
                ['core/group', {
                    className: 'content-top',
                    metadata: {name: 'Obsah hore'},
                    lock: {move: true, remove: true}
                }, [
                    ['core/heading', {level: 3, placeholder: 'Meno projektu', lock: {move: true, remove: true}}],
                    ['core/paragraph', {
                        lock: {move: true, remove: true},
                        placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                    }],
                ]],
                ['core/group', {className: 'content-bottom', metadata: {name: 'Obsah dolu'}}, [
                    ['core/paragraph', {content: '<a href="#" class="stretched-link">Čítať viac</a>'}],
                ]]
            ]]
        ]
    });
