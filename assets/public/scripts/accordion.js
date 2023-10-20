const accordions = document.querySelectorAll('.accordion');

if (accordions.length) {
    accordions.forEach((accordion) => {
        toggle(accordion, 0);

        accordion.querySelectorAll('.accordion-button').forEach((button, index) => {
            button.addEventListener('click', () => {
                toggle(accordion, index);
            });
        });
    });

    function toggle(accordion, expandedIndex) {
        accordion.querySelectorAll('.accordion-item').forEach((item, index) => {
            const expanded = index === expandedIndex;

            const button = item.querySelector('.accordion-button');

            button.classList.toggle('collapsed', false === expanded);
            item.querySelector('.accordion-collapse').classList.toggle('show', expanded);
        });
    }
}
