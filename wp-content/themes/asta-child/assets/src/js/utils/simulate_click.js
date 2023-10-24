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
