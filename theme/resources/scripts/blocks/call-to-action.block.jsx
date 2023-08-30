import {InnerBlocks, useBlockProps} from '@wordpress/block-editor';

const TEMPLATE = [
    ['core/heading', {content: 'Nadpis výzvy k akcii', level: 2}],
    ['core/paragraph', {content: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque at justo viverra metus egestas hendrerit. In sed mauris eget mi vestibulum interdum sit amet ut magna. Sed eu molestie mi. Cras malesuada ut neque vel mollis. Pellentesque sollicitudin quis sapien eu tincidunt. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Fusce metus quam, elementum vitae accumsan tristique, consequat non turpis.'}],
    ['core/button', {text: 'Vestibulum ante ipsum'}]
];

export default {
    name: 'theme/call-to-action',
    title: 'Výzva k akcii',
    description: 'Vytvorte pútavý blok s nadpisom, popisom a tlačidlom.',
    category: 'theme',
    example: {innerBlocks: TEMPLATE.map((i) => ({name: i[0], attributes: i[1]}))},
    edit: () =>
        <div {...useBlockProps()} className={'wp-block-theme-call-to-action'}>
            <InnerBlocks allowedBlocks={['core/button', 'core/heading', 'core/paragraph']} template={TEMPLATE}/>
        </div>,
    save: () => (
        <div {...useBlockProps.save()}>
            <InnerBlocks.Content/>
        </div>
    )
};
