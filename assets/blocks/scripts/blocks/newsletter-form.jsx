const ServerSideRender = window.wp.serverSideRender;
const {BaseControl, CheckboxControl, PanelBody, SelectControl, TextareaControl, TextControl} = window.wp.components;
const {InspectorControls, useBlockProps} = window.wp.blockEditor;
const {registerBlockType} = window.wp.blocks;
const {useSelect} = window.wp.data;

registerBlockType('saleziani/newsletter-form', {
    attributes: {
        description: {
            default: '',
            type: 'string'
        },
        optionals: {
            default: [],
            type: 'array'
        },
        primary: {
            default: 'newsletter',
            type: 'string'
        },
        source: {
            default: 'web-saleziani-sk',
            type: 'string'
        },
        title: {
            default: 'Chcete sledovať, čo máme nové? Pridajte sa do nášho newslettra.',
            type: 'string'
        },
        url: {
            default: 'https://sdbsk.ecomailapp.cz/public/subscribe/1/43c2cd496486bcc27217c3e790fb4088',
            type: 'string'
        }
    },
    category: 'theme',
    description: 'Získajte nových adresátov vašich newslettrov.',
    example: {},
    icon: 'email',
    title: 'Newsletter formulár',
    edit: ({attributes, setAttributes, name}) => {
        const newsletters = useSelect((select) => select('core').getEntityRecords('postType', 'newsletter', {_fields: 'id,slug,title', per_page: -1})) ?? [];

        return <>
            <InspectorControls>
                <PanelBody
                    title={'Nastavenia'}
                    initialOpen={true}
                >
                    <TextControl
                        label={'Nadpis'}
                        value={attributes.title}
                        onChange={(value) => setAttributes({title: value})}
                    />
                    <TextControl
                        label={'Zdroj'}
                        value={attributes.source}
                        onChange={(value) => setAttributes({source: value})}
                    />
                    <TextControl
                        label={'URL'}
                        value={attributes.url}
                        onChange={(value) => setAttributes({url: value})}
                    />
                    <TextareaControl
                        label={'Popis'}
                        value={attributes.description}
                        onChange={(value) => setAttributes({description: value})}
                    />
                    <SelectControl
                        label={'Primárny newsletter'}
                        value={attributes.primary}
                        options={[
                            ...newsletters.map((newsletter) => ({
                                label: newsletter.title.rendered,
                                value: newsletter.slug
                            }))
                        ]}
                        onChange={(value) => setAttributes({primary: value})}
                    />
                    <BaseControl label={'Voliteľné newslettre'}/>
                    {newsletters.filter(newsletter => attributes.primary !== newsletter.slug).map((newsletter, index) =>
                        <CheckboxControl
                            checked={attributes.optionals.includes(newsletter.id)}
                            key={index}
                            label={newsletter.title.rendered}
                            onChange={(value) =>
                                setAttributes({
                                    optionals: (value ?
                                        [...attributes.optionals, newsletter.id].sort((a, b) => a - b) :
                                        attributes.optionals.filter(id => id !== newsletter.id))
                                })}
                        />
                    )}
                </PanelBody>
            </InspectorControls>
            <ServerSideRender attributes={attributes} block={name}/>
        </>;
    }
});
