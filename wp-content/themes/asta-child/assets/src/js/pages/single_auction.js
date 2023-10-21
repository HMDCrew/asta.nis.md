import { sendHttpReq } from '../utils/api'
import { Fancybox } from '@fancyapps/ui'
import Swiper from 'swiper'
import { Thumbs } from 'swiper/modules'
import { wpr_alert } from '../utils/helpers'
import 'swiper/css';
import 'swiper/css/navigation';
import '@fancyapps/ui/dist/fancybox/fancybox.css'

import '../../scss/views/single_auction.scss'

const { auction_id, json_url, nonce } = auction_data;
// const refresh_timeout = 2500;
const refresh_timeout = 500000000;

const bids_list = document.querySelector('.bids-list')
const bids_content = bids_list && bids_list.querySelector('.content')
const button_bid = document.querySelector('.bid-now')
const last_price = document.querySelector('.wrap-input.last_price input')
const auction_date = document.querySelector('.wrap-input.auction_date input')


const swiper = new Swiper(".thumbnails", {
    loop: true,
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});

const next = document.querySelector('.auction-gallery .swiper-button-next')
const prev = document.querySelector('.auction-gallery .swiper-button-prev')

new Swiper(".auction-gallery", {
    loop: true,
    modules: [Thumbs],
    spaceBetween: 10,
    on: {
        init: (swipper) => {

            Fancybox.bind('[data-fancybox="gallery"]')

            if (swipper.slides.length > 0) {
                next.addEventListener('click', ev => swipper.slideNext(), false)
                prev.addEventListener('click', ev => swipper.slidePrev(), false)
            } else {
                prev.classList.add('swiper-button-disabled')
                next.classList.add('swiper-button-disabled')
            }
        },
    },
    thumbs: {
        swiper: swiper,
    },
});


if (button_bid) {

    /**
     * The function formats a given date into a specific string format.
     * @param bid_date - The input parameter `bid_date` is a string representing a date and time in a
     * specific format. The function `format_date` converts this string into a formatted date and time
     * string in a different format.
     * @returns The function `format_date` returns a formatted date string in the format of `DD/MM/YYYY
     * HH:MM:SS`. The input `bid_date` is converted to a `Date` object, and then the date, month, year,
     * hours, minutes, and seconds are extracted and formatted with leading zeros if necessary. The
     * resulting string is then returned.
     */
    const format_date = (bid_date) => {

        const date = new Date(bid_date)
        // remove timezone autoupdate and use server time zone
        date.setTime(date.getTime() + date.getTimezoneOffset() * 60 * 1000);

        const zero_cheat = (val) => {
            return val < 10 ? '0' + val : val;
        }

        return `${zero_cheat(date.getDate())}/${(zero_cheat(date.getMonth() + 1))}/${(zero_cheat(date.getFullYear()))} ${zero_cheat(date.getHours())}:${zero_cheat(date.getMinutes())}:${zero_cheat(date.getSeconds())}`
    }


    /**
     * The function creates a new div element with a specified class name and content, and returns it.
     * @param class_name - The class name that will be added to the newly created div element.
     * @param [content] - The `content` parameter is an optional parameter that represents the
     * innerHTML content of the div element that will be created. If no content is provided, the div
     * element will be created with an empty innerHTML.
     * @returns The function `reder_div` is returning a newly created `div` element with the specified
     * class name and content.
     */
    const reder_div = (class_name, content = '') => {
        const div = document.createElement('div');
        div.classList.add(class_name)
        div.innerHTML = content;
        return div;
    }


    /**
     * The function `render_bid` takes a bid object and returns a container element with various
     * properties of the bid displayed within it.
     * @param bid - The parameter `bid` is an object that contains information about a bid, such as the
     * user who made the bid, the date it was made, the amount by which the bid increased the price,
     * and the current price after the bid was made.
     * @returns The `render_bid` function is returning a container element that contains several child
     * elements created using the `render_div` function. These child elements display information about
     * a bid, including the user's display name, the date of the bid, the price increment, and the
     * current price.
     */
    const render_bid = (bid) => {
        const container = reder_div('bid-element')
        container.append(reder_div('display_name', bid.user.display_name))
        container.append(reder_div('date', format_date(bid.date)))
        container.append(reder_div('price_increment', `+ ${bid.price_increment}`))
        container.append(reder_div('now_price', bid.now_price))
        return container;
    }


    /**
     * The function refreshes the bids list and updates the last bid price if it has changed.
     * @param response_bids - It is a parameter that represents the response object containing the bids
     * data. The function `refresh_bids` takes this parameter and updates the bids list on the webpage
     * based on the latest bid data received in the response.
     */
    const refresh_bids = (response_bids) => {

        const bids = Object.values(response_bids);
        const last_bid = bids[bids.length - 1];

        if (parseFloat(last_price.value) !== parseFloat(last_bid.now_price)) {

            bids_list.classList.remove('hide')
            bids_content.innerHTML = '';

            bids.forEach(bid => bids_content.append(render_bid(bid)));

            button_bid.querySelector('.last-price').innerHTML = parseFloat(last_bid.now_price) + parseFloat(last_bid.price_increment)
            last_price.value = parseFloat(last_bid.now_price)
        }
    }


    /**
     * The function handles different response states for bids and either refreshes bids, displays a
     * warning message, or redirects to the login page.
     * @param res - The parameter `res` is an object that contains information about the response from
     * a server. It is likely that this function is used to handle the response from a server after
     * making a request for bids. The `res` object likely contains a `status` property that indicates
     * whether the request was successful or
     */
    const bids_response_states = (res) => {
        switch (res.status) {
            case 'success':
                refresh_bids(res.bids)
                break;

            case 'worring':
                wpr_alert([res.message], 'worring')
                break;

            default:
                wpr_alert([res.message])
                window.location.replace('/login')
                break;
        }
    }


    /**
     * The function validates if the current date is within a specified start and end date for an
     * auction.
     * @returns The function `validate_date` is returning a boolean value indicating whether the
     * current date and time is within the range specified by the `start_date` and `end_date`
     * attributes of the `auction_date` element. If the current date and time is within the range,
     * `true` is returned, otherwise `false` is returned.
     */
    const validate_date = () => {
        const date = new Date();

        const start_date = new Date(auction_date.getAttribute('start_date'))
        start_date.setTime(start_date.getTime() + start_date.getTimezoneOffset() * 60 * 1000);

        const end_date = new Date(auction_date.getAttribute('end_date'))
        end_date.setTime(end_date.getTime() + end_date.getTimezoneOffset() * 60 * 1000);

        return date < end_date && date > start_date;
    }


    /**
     * This function sends a POST request to a specified URL with data and headers, then parses the
     * response and passes it to another function.
     */
    const new_bid = () => {

        if (validate_date()) {
            sendHttpReq({
                url: json_url + 'api-auction-new-bid',
                method: 'POST',
                data: { auction_id },
                headers: { 'X-WP-Nonce': nonce }
            }).then(res => {
                res = JSON.parse(res);

                bids_response_states(res)

            }).catch(e => { console.log(e) });
        } else {
            wpr_alert(['Auction isn\'t start'], 'worring')
        }
    }
    button_bid && button_bid.addEventListener('click', (ev) => new_bid(), false)


    /**
     * The function updates the bids list by sending a POST request to a JSON API endpoint and handling
     * the response.
     */
    const update_bids_list = () => {

        if (validate_date()) {

            sendHttpReq({
                url: json_url + 'api-auction-bids',
                method: 'POST',
                data: { auction_id },
                headers: { 'X-WP-Nonce': nonce }
            }).then(res => {
                res = JSON.parse(res);

                bids_response_states(res)

                if (res.invok_nexts) {
                    setTimeout(() => update_bids_list(), refresh_timeout)
                }

            }).catch(e => { console.log(e) });
        }
    }

    setTimeout(() => update_bids_list(), refresh_timeout)
}