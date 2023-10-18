import '../../scss/views/auth_user.scss'

const admin_menu = document.querySelector('.wpr-admin-menu')
if (admin_menu) {

    const main_button = admin_menu.querySelector('.open-admin-menu');

    /**
     * The function toggles the 'opened' class on the parent node of the main button.
     */
    const toggle_admin_menu = () => {
        main_button.parentNode.classList.toggle('opened')
    }

    main_button.addEventListener('click', (ev) => toggle_admin_menu(), false)


    const close_menu_on_scroll = () => {
        if (main_button.parentNode.classList.contains('opened')) {
            main_button.parentNode.classList.remove('opened')
        }
    }

    document.addEventListener("scroll", (event) => close_menu_on_scroll());
}