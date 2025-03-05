const {registerPlugin} = window.wp.plugins;
const {PluginPostStatusInfo} = window.wp.editPost;
const {TextControl} = window.wp.components;
const {withSelect, withDispatch} = window.wp.data;
const {compose} = window.wp.compose;

export const registerMetaTextField = (label, name) => {
    let Field = (props) => {
        console.log(props);
        return <PluginPostStatusInfo>
            <TextControl label={label} value={props[name]} onChange={value => props.onChange(value)}/>
        </PluginPostStatusInfo>;
    };

    Field = compose([
        withSelect(select => {
            const meta = select('core/editor').getEditedPostAttribute('meta');

            return ({[name]: meta ? meta[name] : ''});
        }), withDispatch(dispatch => ({
            onChange: (value) => {
                dispatch('core/editor').editPost({meta: {[name]: value}});
            }
        }))
    ])(Field);

    registerPlugin(name, {render: Field});
}
