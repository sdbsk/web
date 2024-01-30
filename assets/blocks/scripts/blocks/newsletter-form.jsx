const ServerSideRender = window.wp.serverSideRender;
const {registerBlockType} = window.wp.blocks;
const {InspectorControls, useBlockProps} = window.wp.blockEditor;
const {PanelBody, TextControl} = window.wp.components;

// registerBlockType('saleziani/top-level-page-title', {
//     title: 'test',
//     edit: ({attributes, name}) => <div>
//         <ServerSideRender attributes={attributes} block={name} key={'preview'}/>
//     </div>
// });

registerBlockType('saleziani/newsletter-form', {
    attributes: {
        title: {
            default: 'Chcete sledovať, čo máme nové? Pridajte sa do nášho newslettra.',
            type: 'string'
        },
        source: {
            default: 'web-saleziani-sk',
            type: 'string'
        }
    },
    category: 'theme',
    description: 'Získajte nových adresátov vašich newslettrov.',
    example: {},
    icon: 'email',
    title: 'Newsletter formulár',
    edit: ({attributes, setAttributes, name}) => {
        return <div>
            <InspectorControls key="setting">
                <PanelBody
                    title={'Settings'}
                    initialOpen={true}
                >
                    <TextControl
                        label="Title"
                        value={attributes.title}
                        onChange={(value) => setAttributes({title: value})}
                    />
                    <TextControl
                        label="Source"
                        value={attributes.source}
                        onChange={(value) => setAttributes({source: value})}
                    />
                </PanelBody>
            </InspectorControls>
            <ServerSideRender attributes={attributes} block={name} key={'preview'}/>
        </div>
    }
});
