// import * as iconComponents from 'react-icons/fa6';
// import {IconContext} from "react-icons";
// import {GenIcon} from "react-icons/lib";
// import {FaImage} from "react-icons/fa6";

import '@material-symbols/font-300/outlined.css';
import iconNames from "../scripts/icon-names";

const {useEffect, useState} = window.React;

const {Button, TextControl} = window.wp.components;

// const icon = 'face';
// const {default: Face} = await import(`@material-design-icons/svg/filled/face.svg`);

// import icons from '@material-design-icons/svg/filled';

// console.log(icons);

// const iconComponentsAll = {
//     pray: (props) => GenIcon({
//         "tag": "svg", "attr": {"viewBox": "0 0 32 32"}, "child": [{
//             "tag": "path",
//             "attr": {"d": "M20.5 21v-3.6l-1.4-2.7c-.3 0-.6.2-.8.5-.2.2-.3.5-.3.9v7.3l3.2 5.3h-2.4L16 24v-8c0-.6.2-1.3.6-1.8.3-.6.8-1 1.5-1.3l-2-3.7c-.5-.8-.7-1.6-.6-2.5 0-.8.4-1.6 1-2.2l2-2 8.8 10.4 1.4 15.8h-2l-1.3-15-7-8.3-.5.5c-.3.3-.4.6-.5 1 0 .3 0 .7.2 1l5 9V21h-2Zm-11 0v-4.2L14.4 8c.2-.3.2-.7.2-1 0-.4-.2-.7-.5-1l-.5-.5-7 8.3-1.3 15h-2l1.4-15.8 8.8-10.4 2 2c.6.6 1 1.4 1 2.2 0 .9-.1 1.7-.5 2.5l-2 3.7a3.5 3.5 0 0 1 2 3.2V24l-2.8 4.7h-2.4l3.2-5.3V16a1.4 1.4 0 0 0-1-1.4l-1.5 2.7V21h-2Z"}
//         }]
//     })(props)
// };

const cachedAsyncIcons = {};
const icons = {};

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

