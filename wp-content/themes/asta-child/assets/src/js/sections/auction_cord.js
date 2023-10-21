/**
 * The function renders a date with an icon in HTML format.
 * @param date - The parameter "date" is a string representing a date in any format. It is used to
 * render a date icon and the date itself in a specific HTML format.
 * @returns The function `render_date` returns an HTML string that contains a date wrapped in a `div`
 * element with a class of `date`. The date is displayed along with an SVG icon of a calendar. If the
 * `date` argument passed to the function is an empty string or contains only whitespace characters, an
 * empty string is returned.
 */
const render_date = (date) => {
    return ('' !== date.trim()
        ? `<div class="date">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M152 24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H64C28.7 64 0 92.7 0 128v16 48V448c0 35.3 28.7 64 64 64H384c35.3 0 64-28.7 64-64V192 144 128c0-35.3-28.7-64-64-64H344V24c0-13.3-10.7-24-24-24s-24 10.7-24 24V64H152V24zM48 192H400V448c0 8.8-7.2 16-16 16H64c-8.8 0-16-7.2-16-16V192z"/></svg>
                ${date}
            </div>`
        : ''
    );
}


/**
 * The function renders an HTML element containing an auction type name and link, with an accompanying
 * SVG icon.
 * @param auction_type - The parameter `auction_type` is an object that contains information about a
 * type of auction, including its name and link. The function `render_type` takes this object as input
 * and returns a string of HTML code that displays the name and link of the auction type, along with an
 * icon. If the
 * @returns The function `render_type` returns a string of HTML code that includes an SVG icon and a
 * link to an auction type, if the `auction_type` parameter is not an empty string or null. If
 * `auction_type` is empty or null, an empty string is returned.
 */
const render_type = (auction_type) => {
    return (
        auction_type && auction_type?.link && auction_type?.name
            ? `<div class="auction_type">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M0 96C0 60.7 28.7 32 64 32H512c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zM128 288a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm32-128a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM128 384a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm96-248c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H448c13.3 0 24-10.7 24-24s-10.7-24-24-24H224z"/></svg>
                <a href="${auction_type.link}">${auction_type.name}</a>
            </div>`
            : ''
    );
}


/**
 * The function `render_excerpt` returns a div element containing the post excerpt if it is not empty,
 * otherwise it returns an empty string.
 * @param post_excerpt - `post_excerpt` is a variable that contains the excerpt (a short summary or
 * description) of a blog post. It is passed as a parameter to the `render_excerpt` function.
 * @returns The `render_excerpt` function returns a string that represents an HTML element containing
 * the post excerpt. If the `post_excerpt` parameter is not an empty string after trimming, the
 * function returns a `div` element with the class `post_excerpt` and the content of the `post_excerpt`
 * parameter. Otherwise, it returns an empty string.
 */
const render_excerpt = (post_excerpt) => {
    return (
        '' !== post_excerpt.trim()
            ? `<div class="post_excerpt">${post_excerpt}</div>`
            : ''
    );
}


/**
 * The function `render_price` generates HTML code for displaying a base price and an optional price
 * increment.
 * @param baze_price - The base price of a product or service.
 * @param price_increment - `price_increment` is a numeric value representing the amount by which the
 * base price is increased. It is used to display the increment value next to the base price in the
 * rendered HTML output.
 * @returns The function `render_price` returns a string of HTML markup that displays the base price
 * and an optional price increment. If the `baze_price` argument is not an empty string, the function
 * returns a div element with the class `price-info` that contains two child div elements with the
 * classes `baze_price` and `price_increment`, respectively. The `baze_price` div contains an
 */
const render_price = (baze_price, price_increment) => {

    const price =
        `<div class="baze_price">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M160 0c17.7 0 32 14.3 32 32V67.7c1.6 .2 3.1 .4 4.7 .7c.4 .1 .7 .1 1.1 .2l48 8.8c17.4 3.2 28.9 19.9 25.7 37.2s-19.9 28.9-37.2 25.7l-47.5-8.7c-31.3-4.6-58.9-1.5-78.3 6.2s-27.2 18.3-29 28.1c-2 10.7-.5 16.7 1.2 20.4c1.8 3.9 5.5 8.3 12.8 13.2c16.3 10.7 41.3 17.7 73.7 26.3l2.9 .8c28.6 7.6 63.6 16.8 89.6 33.8c14.2 9.3 27.6 21.9 35.9 39.5c8.5 17.9 10.3 37.9 6.4 59.2c-6.9 38-33.1 63.4-65.6 76.7c-13.7 5.6-28.6 9.2-44.4 11V480c0 17.7-14.3 32-32 32s-32-14.3-32-32V445.1c-.4-.1-.9-.1-1.3-.2l-.2 0 0 0c-24.4-3.8-64.5-14.3-91.5-26.3c-16.1-7.2-23.4-26.1-16.2-42.2s26.1-23.4 42.2-16.2c20.9 9.3 55.3 18.5 75.2 21.6c31.9 4.7 58.2 2 76-5.3c16.9-6.9 24.6-16.9 26.8-28.9c1.9-10.6 .4-16.7-1.3-20.4c-1.9-4-5.6-8.4-13-13.3c-16.4-10.7-41.5-17.7-74-26.3l-2.8-.7 0 0C119.4 279.3 84.4 270 58.4 253c-14.2-9.3-27.5-22-35.8-39.6c-8.4-17.9-10.1-37.9-6.1-59.2C23.7 116 52.3 91.2 84.8 78.3c13.3-5.3 27.9-8.9 43.2-11V32c0-17.7 14.3-32 32-32z"></path></svg>
            ${baze_price}
        </div>`;

    const increment = `<div class="price_increment">(+${price_increment})</div>`;

    return (
        '' !== baze_price.toString().trim()
            ? `<div class="price-info">
                <div class="price-info-row">
                    ${price}
                    ${'' !== price_increment ? increment : ''}
                </div>
            </div>`
            : ''
    );
}


/**
 * The function returns an HTML edit button with a link to edit an auction if the show_button parameter
 * is true.
 * @param show_button - A boolean value that determines whether or not to show the edit button.
 * @param auction_id - auction_id is a variable that represents the unique identifier of an auction. It
 * is used in the function to generate a link to the edit page of the auction with the specified ID.
 * @param label - The label parameter is a string that represents the text that will be displayed on
 * the button.
 * @returns The function `render_edit_btn` returns a string that contains an HTML anchor element
 * (`<a>`) with a link to edit an auction, if the `show_button` parameter is true. If `show_button` is
 * false, an empty string is returned. The link includes the `auction_id` parameter and the `label`
 * parameter is used as the text displayed on the button.
 */
const render_edit_btn = (show_button, auction_id, label) => {
    return show_button ? `<a href="/edit-auction/?auction_id=${auction_id}" class="btn btn-primary d-inline-flex edit-auction">${label}</a>` : '';
}


/**
 * The function `render_card` creates an HTML element for an auction post with various properties such
 * as title, image, date, type, excerpt, and price.
 * @param id - The unique identifier for the auction post.
 * @param post_title - The title of the post/auction.
 * @param url - The URL of the auction post.
 * @param image - The URL of the image to be displayed in the auction card.
 * @param auction_date - The date of the auction.
 * @param auction_type - The type of auction, which is likely a string value such as "online", "live",
 * or "silent".
 * @param post_excerpt - post_excerpt is a string parameter that represents a short summary or
 * description of the auction post. It is used in the HTML template to render the excerpt section of
 * the post.
 * @param baze_price - The base price of the auction.
 * @param price_increment - price_increment is a numeric value representing the amount by which the
 * bidding price will increase in an auction.
 * @returns {HTMLElement} The function `render_card` is returning an HTML element (specifically, an `article`
 * element) that contains various pieces of information about an auction, including its title, image,
 * date, type, excerpt, and price, as well as buttons for viewing the auction details and editing the
 * auction (if the user is the owner of the auction).
 */
export const render_card = (id, post_title, url, image, auction_date, auction_type, post_excerpt, baze_price, price_increment, is_my_auction, labels) => {

    const auction = document.createElement('article')
    auction.id = 'post-' + id
    auction.classList.add('post-' + id)
    auction.classList.add('auctions')
    auction.classList.add('type-auctions')
    auction.classList.add('status-publish')
    auction.classList.add('hentry')

    auction.innerHTML =
        `<a href="${url}" rel="bookmark">
            <img class="thumbnail auction-thumbnail" src="${image}">
        </a>
        <div class="entry-content">
            <h2 class="entry-title"><a href="${url}" rel="bookmark">${post_title}</a></h2>
            ${render_date(auction_date)}
            ${render_type(auction_type)}
            ${render_excerpt(post_excerpt)}
            ${render_price(baze_price, price_increment)}
            <div class="actions ${is_my_auction ? 'd-flex' : ''}">
                <a href="${url}" class="btn btn-primary auction-details">${labels['details']}</a>
                ${render_edit_btn(is_my_auction, id, labels['edit'])}
            </div>
        </div>`;

    return auction;
}