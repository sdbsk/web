const {registerPlugin} = window.wp.plugins;
const {PluginPostStatusInfo} = window.wp.editPost;
const {TextControl} = window.wp.components;
const {withSelect, withDispatch} = window.wp.data;
const {compose} = window.wp.compose;

let DomicilField = (props) => (
    <PluginPostStatusInfo>
        <TextControl label="Domicil" value={props.domicil} onChange={newDomicil => props.onDomicilChange(newDomicil)}/>
    </PluginPostStatusInfo>
);

DomicilField = compose([
    withSelect(select => {
        const meta = select('core/editor').getEditedPostAttribute('meta');

        return ({
            domicil: meta ? meta.domicil : ''
        });
    }),
    withDispatch(dispatch => ({
        onDomicilChange: (value) => {
            dispatch('core/editor').editPost({meta: {domicil: value}});
        }
    }))
])(DomicilField);

registerPlugin('domicil', {render: DomicilField});
