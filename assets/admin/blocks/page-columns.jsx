import registerColumnsBlock from "../scripts/registerColumnsBlock";

registerColumnsBlock(
    'page', {
        parentTitle: 'Odkazy na stránky',
        childTitle: 'Stránka',
        // defaultBackgroundColor: 'yellow',
        minColumnCount: 2,
        maxColumnCount: 3,
        template: [
            ['saleziani/link-to-page', {lock: {move: true, remove: true}}],
        ]
    });
