export default {
    name: 'theme/latest-posts',
    title: 'Najnovšie články',
    category: 'theme',
    edit: () => <div>
        <h3>Blok "Najnovšie články"</h3>
        <p>Tu sa zobrazia tri najnovšie články z <a href={'/wp/wp-admin/options-writing.php'}>predvolenej kategórie</a>.</p>
    </div>
};
