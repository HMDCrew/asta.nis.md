import flatpickr from 'flatpickr'
import { sendHttpReq } from '../utils/api/http';
import { render_auction_card } from '../sections/auction_cord'
import { render_shop_card } from '../sections/shop_card'
import { get_filter_data } from '../utils/get_filter_data'

const filter_bar = document.querySelector('.filter-bar')
const load_more = document.querySelector('.load-more')

export class FilterBar {

    constructor(endpoint, handle_add_to_cart, args) {
        if (filter_bar) {

            this.endpoint = endpoint
            this.handle_add_to_cart = handle_add_to_cart

            this.nonce = args.nonce
            this.labels = args.labels
            this.user_id = args.user_id

            this.search = filter_bar.querySelector('.search input[name="search"]')
            this.type = filter_bar.querySelector('.search .wrap-input input.select')
            this.price_range = filter_bar.querySelector('#price_range')

            this.mobile_filter_btn = document.querySelector('.mobile-filter-btn')

            this.timeout_id = null
            this.is_first_request = true

            this.setup_price_range()
            this.setup_date_range()

            this.search.addEventListener('keyup', (ev) => this.on_change_filter(), false)
            this.type && new MutationObserver(() => this.on_change_filter()).observe(this.type, { attributes: true })
            this.price_range.addEventListener('change', (ev) => this.on_change_filter(), false)

            this.mobile_filter_btn.addEventListener('click', (ev) => filter_bar.classList.toggle('visible'), false)
        }
    }


    /**
     * The function sets the width of the range slider to 100% if the price range exists.
     */
    setup_price_range() {
        if (this.price_range) {
            this.price_range.shadowRoot.querySelector('#range-slider').style.width = '100%'
        }
    }


    /**
     * The function sets up a date range picker using the flatpickr library and triggers a callback
     * function when the date range is changed.
     */
    setup_date_range() {

        const date_range = document.querySelector('.filter-bar #date-range')

        if (date_range) {

            flatpickr(".filter-bar #date-range", {
                mode: "range",
                dateFormat: "d/m/Y",
                onClose: () => this.on_change_filter(),
            })
        }
    }


    /**
     * The function `append_content` appends content to a specified container based on the provided
     * JSON data, with options to replace existing content and handle different types of cards.
     * @param json - The JSON object that contains the response data from the server.
     * @param cards_container - The `cards_container` parameter is a reference to an HTML element that
     * serves as a container for displaying cards. It is used to append new cards or replace existing
     * cards based on the `replace` parameter.
     * @param [replace=false] - A boolean value indicating whether to replace the existing content in
     * the `cards_container` or append to it. If `replace` is `true`, the existing content will be
     * removed before appending new content. If `replace` is `false`, the new content will be appended
     * to the existing content.
     */
    append_content(json, cards_container, replace = false) {
        if (replace) {
            cards_container.classList.remove('loading')
            cards_container.innerHTML = ''
        }

        if ('success' === json.status && cards_container) {


            json.message.forEach(async el => {

                if (el.hasOwnProperty('is_my_auction')) {

                    const card = render_auction_card(
                        el.ID, el.post_title, el.guid,
                        el.image, el.auction_date, el.auction_type,
                        el.post_excerpt, el.price,
                        el.price_increment, el.is_my_auction, this.labels
                    )

                    cards_container.append(card)
                }

                if (el.hasOwnProperty('is_my_product')) {

                    const card = render_shop_card(
                        el.ID, el.post_title, el.guid,
                        el.image, el.post_excerpt,
                        el.price, el.is_my_product, this.labels
                    )

                    const add_to_cart = card.querySelector('.add-to-cart')
                    add_to_cart.addEventListener('click', ev => this.handle_add_to_cart(add_to_cart), false)

                    cards_container.append(card)
                }
            })
        }

        if ('error' === json.status && !Array.isArray(json.message)) {

            const old_no_prod_message = cards_container.querySelector('.no-products')
            old_no_prod_message && old_no_prod_message.remove()

            const message = document.createElement('p')
            message.classList.add('position-absolute')
            message.classList.add('w-100')
            message.classList.add('no-products')
            message.innerHTML = json.message

            cards_container.append(message)
        }

        if (json.hasOwnProperty('is_lasts') && json.is_lasts) {
            load_more && load_more.classList.add('hide')
        } else {
            load_more && load_more.classList.remove('hide')
        }
    }


    /**
     * The function `build_request()` sends an HTTP request to a specified endpoint with filter data
     * and updates the content on the webpage based on the response.
     */
    build_request() {

        const data = get_filter_data(1, this.user_id)

        if (
            (
                '' !== data.search.trim() ||
                data.type ||
                (data.date_rage && data.date_rage[0] && data.date_rage[1]) ||
                data.price_range[0].toString() !== price_range.getAttribute('min') ||
                data.price_range[1].toString() !== price_range.getAttribute('max')
            ) ||
            !this.is_first_request
        ) {

            const cards_container = document.querySelector('.list-content')
            cards_container.classList.add('loading')
            cards_container.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_aj0A{transform-origin:center;animation:spinner_KYSC .75s infinite linear}@keyframes spinner_KYSC{100%{transform:rotate(360deg)}}</style><path d="M12,4a8,8,0,0,1,7.89,6.7A1.53,1.53,0,0,0,21.38,12h0a1.5,1.5,0,0,0,1.48-1.75,11,11,0,0,0-21.72,0A1.5,1.5,0,0,0,2.62,12h0a1.53,1.53,0,0,0,1.49-1.3A8,8,0,0,1,12,4Z" class="spinner_aj0A"/></svg>'

            sendHttpReq({
                url: this.endpoint,
                data: data,
                headers: { 'X-WP-Nonce': this.nonce }
            }).then(res => {

                this.is_first_request = false

                this.append_content(JSON.parse(res), cards_container, true)

                load_more && load_more.classList.remove('loading')

            }).catch(e => {
                console.log(e)
            })
        }
    }


    /**
     * The function "on_change_filter()" sets a timeout to call the "build_request()" function after 1
     * second.
     */
    on_change_filter() {

        clearTimeout(this.timeout_id)

        this.timeout_id = setTimeout(() => this.build_request(), 1000)
    }
}

export class LoadMore {

    constructor(endpoint, handle_add_to_cart, args) {
        if (load_more) {

            this.endpoint = endpoint
            this.user_id = args.user_id
            this.labels = args.labels
            this.nonce = args.nonce

            this.handle_add_to_cart = handle_add_to_cart

            this.curent_page = 1

            load_more.addEventListener('click', (ev) => this.handle_load_more(ev), false)
        }
    }

    append_content(json, cards_container, replace = false) {

        if (replace) {
            cards_container.classList.remove('loading')
            cards_container.innerHTML = ''
        }

        if ('success' === json.status && cards_container) {

            json.message.forEach(async el => {

                if (el.hasOwnProperty('is_my_auction')) {

                    const card = render_auction_card(
                        el.ID, el.post_title, el.guid,
                        el.image, el.auction_date, el.auction_type,
                        el.post_excerpt, el.price,
                        el.price_increment, el.is_my_auction, this.labels
                    )

                    cards_container.append(card)
                }

                console.log(el)
                if (el.hasOwnProperty('is_my_product')) {

                    const card = render_shop_card(
                        el.ID, el.post_title, el.guid,
                        el.image, el.post_excerpt,
                        el.price, el.is_my_product, this.labels
                    )

                    const add_to_cart = card.querySelector('.add-to-cart')
                    add_to_cart.addEventListener('click', ev => this.handle_add_to_cart(add_to_cart), false)

                    cards_container.append(card)
                }
            })

            this.curent_page++
        }

        if ('error' === json.status && !Array.isArray(json.message)) {

            const old_no_prod_message = cards_container.querySelector('.no-products')
            old_no_prod_message && old_no_prod_message.remove()

            const message = document.createElement('p')
            message.classList.add('position-absolute')
            message.classList.add('w-100')
            message.classList.add('no-products')
            message.innerHTML = json.message

            cards_container.append(message)
        }

        if (json?.is_lasts) {
            load_more.classList.add('hide')
        }
    }

    handle_load_more(ev) {

        ev.preventDefault()
        ev.stopPropagation()

        load_more.classList.add('loading')

        console.log(this.curent_page)

        sendHttpReq({
            url: this.endpoint,
            data: get_filter_data(this.curent_page + 1, this.user_id),
            headers: { 'X-WP-Nonce': this.nonce }
        }).then(res => {

            this.append_content(
                JSON.parse(res),
                document.querySelector('.list-content'),
            )

            console.log(this.curent_page)

            load_more.classList.remove('loading')

        }).catch(e => {
            console.log(e)
        })
    }
}
