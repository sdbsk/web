export default {
    name: 'theme/latest-default-category-posts',
    title: 'Najnovšie články z predvolenej kategórie',
    category: 'theme',
    edit: () => <div>
        <h3>Blok "Najnovšie články z predvolenej kategórie"</h3>
        <p>Tu sa zobrazia najnovšie články z <a href={'/wp/wp-admin/options-writing.php'}>predvolenej kategórie</a>.</p>
    </div>
};
