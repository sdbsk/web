import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

export default {
    name: 'theme/call-to-action',
    title: 'Výzva k akcii',
    description: 'Vytvorte pútavý blok s nadpisom, popisom a tlačidlom.',
    category: 'theme',
    example: {
        innerBlocks: [
            {name: 'core/heading', attributes: {content: 'Nadpis', level: 2}},
            {name: 'core/paragraph', attributes: {content: 'Popis'}},
            {name: 'core/button', attributes: {text: 'Tlačidlo'}}
        ]
    },
    edit: () => (
        <div {...useBlockProps()}>
            <InnerBlocks allowedBlocks={['core/button', 'core/heading', 'core/paragraph']}/>
        </div>
    ),
    save: () => (
        <div {...useBlockProps.save()}>
            <InnerBlocks.Content/>
        </div>
    )
};
