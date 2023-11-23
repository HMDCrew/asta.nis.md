export const renderer = (tag_name, class_list = [], attributes = {}) => {

    const element = document.createElement(tag_name)
    class_list.forEach(async class_name => element.classList.add(class_name))

    Object.entries(attributes).forEach(async ([key, val]) => {
        element.setAttribute(key, val)
    })

    return element
}

export const asta_modal = ( content, actions ) => {

    const old = document.querySelector('.asta-modal')
    old?.remove()

    const overlay = renderer('div', ['asta-modal-overlay', 'overlay'])

    const modal_container = renderer('div', ['modal', 'asta-modal', 'vendor-informations'])
    const modal_content = renderer('div', ['asta-modal-content'])
    const modal_actions = renderer('div', ['asta-modal-actions'])

    content(modal_content)
    actions(modal_actions)

    modal_container.append(modal_content)
    modal_container.append(modal_actions)

    overlay.append(modal_container)

    document.body.append(overlay)
}