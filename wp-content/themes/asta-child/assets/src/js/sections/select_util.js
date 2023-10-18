const select_util = document.querySelectorAll('.wrap-input > select')

if (select_util.length) {


    /**
     * The function sets the value and content attribute of an input element based on the selected
     * option in a container.
     * @param container - The container parameter is a reference to an HTML element that contains the
     * select input and the options. It is used to find the input element and set its value and content
     * attribute based on the selected option.
     * @param option - The `option` parameter is a reference to an HTML element that represents an
     * option in a select dropdown menu. It is passed as an argument to the `on_select_option` function
     * when the user selects an option from the dropdown menu.
     */
    const on_select_option = (container, option) => {
        const input = container.querySelector('input.select')
        input.value = option.textContent;
        input.setAttribute('content', option.getAttribute('value'))
        const options = container.querySelector('div.select_input.visible')
        if (options) {
            options.classList.remove('visible')
        }
    }


    /**
     * The function creates a clone of a given option element and adds event listener to it.
     * @param container - The container parameter is a reference to the HTML element that will contain
     * the cloned option element.
     * @param option - The `option` parameter is a reference to an HTML `option` element that
     * represents an option in a dropdown/select menu.
     * @returns The function `clone_option` is returning a newly created `div` element with the class
     * `option`, an optional `value` attribute (if the original `option` element had one), and the same
     * innerHTML as the original `option` element. The returned `div` element also has an event
     * listener attached to it that calls the `on_select_option` function when clicked, passing in the
     */
    const clone_option = (container, option) => {

        let div_option = document.createElement('div');
        div_option.classList.add('option')

        const opt_val = option.getAttribute('value');
        opt_val && div_option.setAttribute('value', opt_val);
        div_option.innerHTML = option.innerHTML;

        div_option.addEventListener('click', ev => on_select_option(container, div_option), false)

        return div_option
    }


    /**
     * This function creates a read-only text input element that represents a selected option in a
     * dropdown menu and adds an event listener to it.
     * @param selected_opt - This parameter represents the selected option from a dropdown/select menu.
     * It is a DOM element that contains information about the selected option, such as its text
     * content and value.
     * @param interact - `interact` is a function that will be called when the input element created by
     * this function is clicked. It is passed as a parameter to the function and added as an event
     * listener to the input element.
     * @returns The function `input_clone_select_interaction` returns a newly created `input` element
     * with some attributes and event listener added to it.
     */
    const input_clone_select_interaction = (selected_opt, interact) => {

        let input = document.createElement('input');
        input.setAttribute('readonly', true)
        input.setAttribute('type', 'text')
        input.value = selected_opt.textContent

        if (selected_opt.hasAttribute('selected')) {
            input.setAttribute('content', selected_opt.value);
        }

        input.classList.add('input')
        input.classList.add('select')

        input.addEventListener('click', interact, false);

        return input;
    }


    /**
     * The function clones a select element into a div element with options and adds interaction to
     * toggle visibility.
     * @param select - The `select` parameter is a reference to a `<select>` element in the HTML
     * document.
     */
    const clone_to_div = (select) => {
        const container = select.parentNode;
        const options = select.querySelectorAll('option');

        if (options.length) {
            const selected = select.querySelector('option[selected]') || options[0]

            let scroll_area = document.createElement('div');
            scroll_area.classList.add('scroll_area')

            options.forEach(option => scroll_area.append(clone_option(container, option)));

            let div_select = document.createElement('div');
            div_select.classList.add('select_input')
            div_select.classList.add('input')
            div_select.append(scroll_area)

            container.prepend(div_select)
            container.prepend(input_clone_select_interaction(
                selected,
                (event) => {
                    div_select.classList.toggle('visible')
                }
            ))
        }
    }


    select_util.forEach(async select => clone_to_div(select))
}