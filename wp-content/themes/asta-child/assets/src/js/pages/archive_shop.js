import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/dark.css'

import 'toolcool-range-slider/dist/plugins/tcrs-binding-labels.min.js';
import 'toolcool-range-slider';

import '../../scss/views/archive_shop.scss'
import { sendHttpReq } from '../utils/api';
import { render_card } from '../sections/auction_cord'


const { json_url, nonce, labels, user_id } = shop_data
let curent_page = 1;

const filter_bar = document.querySelector('.filter-bar')
const load_more = document.querySelector('.load-more-auctions')


/**
 * It takes the response from the server, parses it, and then appends the content to the page
 * @param response - the response from the server
 * @param cards_container - the container where the cards will be added
 * @param load_more_btn - the button that triggers the load more action
 * @param curent_page - the current page number
 * @returns The current page number.
 */
const append_content = (json, cards_container, load_more_btn, curent_page, replace = false) => {

    if (replace) {
        cards_container.classList.remove('loading')
        cards_container.innerHTML = '';
    }

    if ('success' === json.status && cards_container) {

        json.message.forEach(el => cards_container.append(render_card(el.ID, el.post_title, el.guid, el.image, el.auction_date, el.auction_type, el.post_excerpt, el.baze_price, el.price_increment, el.is_my_auction, labels)));

        curent_page++;
    }

    if ('error' === json.status && !Array.isArray(json.message)) {

        const old_no_prod_message = cards_container.querySelector('.no-products')
        old_no_prod_message && old_no_prod_message.remove();

        const message = document.createElement('p')
        message.classList.add('position-absolute')
        message.classList.add('w-100')
        message.classList.add('no-products')
        message.innerHTML = json.message;

        cards_container.append(message)
    }

    if (json?.is_lasts) {
        load_more_btn && load_more_btn.classList.add('hide');
    }

    return curent_page;
}


/**
 * The function takes a string value and returns an object with a minimum and maximum value if the
 * string is not empty, otherwise it returns false for both values.
 * @param value - The `value` parameter is a string that represents a date range in the format of
 * "start date to end date".
 * @returns The function `get_date_object` returns an object with two properties: `min` and `max`.
 * If the input `value` is not an empty string, it splits the string into an array using the
 * separator " to " and assigns the first element to `min` and the second element to `max`. If the
 * input `value` is an empty string, it returns an object with both
 */
const get_date_object = (value) => {

    if ('' !== value.trim()) {
        const range = value.split(' to ')
        return [range[0], range[1]]
    }

    return [false, false]
}


/**
 * The function returns an object with minimum and maximum values based on the input from a DOM
 * element.
 * @param dom_element - The `dom_element` parameter is an object that represents a DOM element,
 * which is typically an HTML element on a web page. The function is expecting this object to have
 * two properties: `value1` and `value2`, which are expected to be numeric values representing a
 * minimum and maximum price range
 * @returns A JavaScript object with either the minimum and maximum values from the `dom_element`
 * if they are not empty strings, or `false` for both values if either `value1` or `value2` is an
 * empty string.
 */
const get_price_object = (dom_element) => {

    if ('' !== dom_element.value1.toString() && '' !== dom_element.value2.toString()) {
        return [dom_element.value1, dom_element.value2]
    }

    return [false, false]
}


/**
 * The function returns an object containing filter data for auctions, including search terms, type,
 * date range, price range, and page number.
 * @param [page=1] - The page parameter is a number that represents the current page of data being
 * requested. It has a default value of 1 if not provided.
 * @returns The function `get_filter_data` returns an object with properties `search`, `type`,
 * `date_range`, `price_range`, and `page`. The values of these properties are determined based on the
 * presence and values of certain elements in the `filter_bar` element, as well as the `page` argument
 * passed to the function. If any of the required elements are missing, the function returns an
 */
const get_filter_data = (page = 1) => {

    const search = filter_bar && filter_bar.querySelector('.search input[name="search-auctions"]')
    const type = filter_bar && filter_bar.querySelector('.search .wrap-input input.select')
    const date_rage = filter_bar && filter_bar.querySelector('input[name="date-range"]')
    const price_range = filter_bar && filter_bar.querySelector('#price_range')
    const category = type && type.getAttribute('content')

    let data = search && type && date_rage && price_range
        ? {
            search: search ? search.value : '',
            type: category ? category : false,
            date_rage: get_date_object(date_rage.value),
            price_range: get_price_object(price_range),
            page: page,
        }
        : { page: page }

    if (document.body.classList.contains('page-template-my-auctions')) {
        data['user_id'] = user_id
    }

    return data;
}

if (filter_bar) {

    const search = filter_bar.querySelector('.search input[name="search-auctions"]')
    const type = filter_bar.querySelector('.search .wrap-input input.select')
    const price_range = filter_bar.querySelector('#price_range')

    const mobile_filter_btn = document.querySelector('.mobile-filter-btn')

    if (price_range) {
        price_range.shadowRoot.querySelector('#range-slider').style.width = '100%'
    }

    let timeout_id;
    let is_first_request = true;
    const on_change_filter = () => {

        clearTimeout(timeout_id);

        timeout_id = setTimeout(() => {

            const data = get_filter_data()

            if (
                (
                    '' !== data.search.trim() ||
                    data.type ||
                    (data.date_rage[0] && data.date_rage[1]) ||
                    data.price_range[0].toString() !== price_range.getAttribute('min') ||
                    data.price_range[1].toString() !== price_range.getAttribute('max')
                ) ||
                !is_first_request
            ) {

                const cards_container = document.querySelector('.list-auction');
                cards_container.classList.add('loading')
                cards_container.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_aj0A{transform-origin:center;animation:spinner_KYSC .75s infinite linear}@keyframes spinner_KYSC{100%{transform:rotate(360deg)}}</style><path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" class="spinner_aj0A"/></svg>';

                sendHttpReq({
                    url: json_url + 'api-get-shop',
                    data: data,
                    headers: { 'X-WP-Nonce': nonce }
                }).then(res => {

                    is_first_request = false;
                    curent_page = 1;

                    append_content(JSON.parse(res), cards_container, load_more, 0, true);

                    load_more && load_more.classList.remove('loading');

                }).catch(e => {
                    console.log(e);
                });
            }
        }, 1000);
    }

    flatpickr(".filter-bar #date-range", {
        mode: "range",
        dateFormat: "d/m/Y",
        onClose: () => on_change_filter(),
    })

    search.addEventListener('keyup', (ev) => on_change_filter(), false)
    type && new MutationObserver(() => on_change_filter()).observe(type, { attributes: true })
    price_range.addEventListener('change', (ev) => on_change_filter(), false)

    mobile_filter_btn.addEventListener('click', (ev) => filter_bar.classList.toggle('visible'), false)
}


/**
 * This function loads auctions from an API and appends them to a list on the webpage.
 * @param ev - The `ev` parameter is an event object that is passed to the `load_auctions` function. It
 * is used to prevent the default behavior of a form submission and to stop the event from propagating
 * to parent elements.
 */
const load_auctions = (ev) => {

    ev.preventDefault();
    ev.stopPropagation();

    load_more && load_more.classList.add('loading');

    sendHttpReq({
        url: json_url + 'api-get-shop',
        data: get_filter_data(curent_page + 1),
        headers: { 'X-WP-Nonce': nonce }
    }).then(res => {

        curent_page = append_content(
            JSON.parse(res),
            document.querySelector('.list-auction'),
            load_more,
            curent_page
        );

        load_more && load_more.classList.remove('loading');

    }).catch(e => {
        console.log(e);
    });
}

load_more && load_more.addEventListener('click', (ev) => load_auctions(ev), false)
