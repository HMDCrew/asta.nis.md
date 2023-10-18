/**
 * Simulate a click event.
 * @public
 * @param {Element} elem  the element to simulate a click on
 */
export const simulate_click = (elem) => {
    const evt = new MouseEvent('click', {
        bubbles: false,
        cancelable: true,
        view: window
    });

    // If cancelled, don't dispatch our event
    const canceled = !elem.dispatchEvent(evt);
}


/**
 * The function creates an alert message with a specified type and list of messages, which is displayed
 * for 5 seconds before being removed.
 */
export const wpr_alert = (messages = [], type = 'error') => {

    const last_messages = document.querySelector('.wpr_alert')
    last_messages && last_messages.remove()

    let list = document.createElement('div');
    list.classList.add('wpr_alert')
    list.classList.add(`${type}_list`)

    messages.forEach(el => {

        let message = document.createElement('div');
        message.classList.add(type)
        message.classList.add('alert_info')
        message.innerHTML = el;

        list.appendChild(message);
    });

    document.querySelector('body').appendChild(list)

    setTimeout(() => list.remove(), 5000);
}