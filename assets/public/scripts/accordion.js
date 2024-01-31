const accordions = document.querySelectorAll('.accordion');

if (accordions.length) {
    accordions.forEach((accordion) => {
        toggle(accordion, 0);
        accordion.querySelectorAll('.accordion-collapse').forEach((item, index) => {
            let itemBody = item.querySelector('.accordion-body');
            item.style.maxHeight = itemBody.offsetHeight + 'px';
        });
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

            if (expandedIndex === index) {
                if (item.querySelector('.accordion-collapse').classList.contains('hide')) {
                    button.classList.toggle('collapsed', expanded);
                    item.querySelector('.accordion-collapse').classList.toggle('hide', false === expanded);

                } else {
                    button.classList.toggle('collapsed', false === expanded);
                    item.querySelector('.accordion-collapse').classList.toggle('hide', expanded);
                }
            }
        });
    }
}
