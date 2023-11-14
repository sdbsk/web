import registerColumnsBlock from "../scripts/registerColumnsBlock";

registerColumnsBlock(
    'our-project', {
        parentTitle: 'Naše projekty',
        childTitle: 'Projekt',
        defaultBackgroundColor: 'blue',
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
                        level: 3,
                        lock: {move: true, remove: true},
                        placeholder: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
                    }],
                ]],
                ['core/group', {className: 'content-bottom', metadata: {name: 'Obsah dolu'}}, [
                    ['core/paragraph', {level: 3, content: '<a href="#">Čítať viac</a>'}],
                ]]
            ]]
        ]
    });
