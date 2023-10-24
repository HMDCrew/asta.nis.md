/**
 * The function creates an alert message with a specified type and list of messages, which is displayed
 * for 5 seconds before being removed.
 */
export const asta_alert = (messages = [], type = 'error') => {

    const last_messages = document.querySelector('.asta_alert')
    last_messages && last_messages.remove()

    let list = document.createElement('div');
    list.classList.add('asta_alert')
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