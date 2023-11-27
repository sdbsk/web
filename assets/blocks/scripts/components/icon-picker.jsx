import '@material-symbols/font-300/outlined.css';
import iconNames from "../icon-names";

const {useEffect, useState} = window.React;
const {Button, TextControl} = window.wp.components;

export const AsyncIcon = ({name, onLoadedIcon = null}) => {
    if (!name) {
        return <div>no icon</div>
    }

    const [icons, setIcons] = useState({});

    function setIcon(name, icon) {
        if (onLoadedIcon) {
            onLoadedIcon(icon);
        }
        setIcons({...icons, [name]: icon});
    }

    useEffect(() => {
        if (!icons[name]) {
            fetch(`/assets/icon/${name}.svg`)
                .then(response => response.status === 200 ? response.text() : 'not found')
                .then(text => setIcon(name, text))
                .catch((error) => console.log(error));
        }
    }, [name]);

    return <div style={{width: 64, height: 64}}>
        {icons[name] ? <div dangerouslySetInnerHTML={{__html: icons[name]}}/> :
            <div style={{fontSize: '1rem'}}>loading</div>}
    </div>
}

const IconPicker = ({onSelectedIcon, onLoadedIcon, selectedIcon, color = 'black'}) => {
    const [currentPage, setCurrentPage] = useState(0);
    const [searchQuery, setSearchQuery] = useState('');
    const pageSize = 200;

    const icons = iconNames.filter((iconName) => iconName.includes(searchQuery));

    const Paginator = () => <div style={{
        overflowWrap: 'break-word'
    }}>
        <h3 style={{marginBottom: '0.5rem', marginTop: '1.5rem'}}>Pages</h3>

        {Array(Math.ceil(icons.length / pageSize)).keys().map((page) => page === currentPage ? <span style={{
                margin: '0 0.5rem'
            }}>{page + 1}</span> :
            <a href={'#'} style={{
                margin: '0 0.5rem'
            }} onClick={() => setCurrentPage(page)}>{page + 1}</a>)}
    </div>;

    return (<div className={'icon-picker-wrapper'}>
        {selectedIcon &&
            <div>
                <h3 style={{marginBottom: '0.5rem'}}>Selected Icon</h3>
                <AsyncIcon name={selectedIcon} onLoadedIcon={onLoadedIcon}/>
            </div>}

        <TextControl
            label="Filter Icon By Name"
            value={ searchQuery }
            onChange={ ( value ) => {
                setSearchQuery(value)
                setCurrentPage(0);
            }}
        />

        <Paginator />

        <h3 style={{marginBottom: '0.5rem', marginTop: '1.5rem'}}>Icons</h3>

        {icons.slice(currentPage * pageSize, (currentPage + 1) * pageSize - 1).map((iconName, index) => {
            return <Button key={index} variant="secondary"
                           style={{margin: '0.1rem', outlineColor: '#dedede', boxShadow: 'none', padding: 0}}
                           icon={<span className="material-symbols-outlined"
                                       style={{fontSize: '2.5rem'}}>{iconName}</span>}
                           onClick={() => {
                               onSelectedIcon(iconName)
                           }}></Button>
        })}

        <Paginator />
    </div>)
}

export default IconPicker;

