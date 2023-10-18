const masthead = document.querySelector('#masthead');

if (masthead) {

    const open_mobile = masthead.querySelector('.mobile-menu-toggle')

    /**
     * It returns true if the user is scrolling up, and false if the user is scrolling down.
     * @param event - The event object that is passed to the event handler.
     * @returns A boolean value.
     */
    const checkScrollDirectionIsUp = (event) => {

        if (event.wheelDelta) {
            return event.wheelDelta > 0;
        }

        return event.deltaY < 0;
    }

    /**
     * If the user is scrolling up, remove the class 'closed' from the middle row. If the user is scrolling
     * down, add the class 'closed' to the middle row
     * @param event - The event object that is passed to the event handler.
     */
    const checkScrollDirection = (event) => {

        const is_up = checkScrollDirectionIsUp(event);

        if (document.documentElement.scrollTop > 0) {
            if (is_up && masthead.classList.contains('scroll_down')) {
                masthead.classList.remove('scroll_down')
            } else if (!is_up && !masthead.classList.contains('scroll_down')) {
                masthead.classList.add('scroll_down')
            }
        }
    }

    document.body.addEventListener('wheel', (ev) => checkScrollDirection(ev));

    /**
     * Burger section
     */
    const open_burger_mobile = () => {
        open_mobile.parentNode.parentNode.classList.add('opened');
    }

    const close_burger_mobile = () => {
        open_mobile.parentNode.parentNode.classList.remove('opened');
    }

    const toggle_burger = () => {
        if (open_mobile.parentNode.parentNode.classList.contains('opened')) {
            close_burger_mobile();
        } else {
            open_burger_mobile();
        }
    }

    open_mobile && open_mobile.addEventListener('click', (ev) => toggle_burger(), false);
}