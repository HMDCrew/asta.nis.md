import 'swiper/css';
import 'swiper/css/navigation';
import flatpickr from "flatpickr";
import 'flatpickr/dist/themes/dark.css'

import '../../scss/views/new_auction.scss'
import { sendHttpReq } from '../utils/api/http'
import { asta_alert } from '../utils/asta_alert'

const { json_url, nonce } = auction_data;


/**
 * This is a function that sends a simple POST request with optional data and returns a response or
 * error.
 * @param partial_url - The partial URL is a string that represents the endpoint of the API that the
 * HTTP POST request will be sent to. It is usually a part of the full URL and specifies the specific
 * resource or action that the request is targeting.
 * @param data - The `data` parameter is an optional parameter that represents the data to be sent in
 * the request body of the HTTP POST request. It can be an object, string, or any other valid data type
 * that can be sent in the request body. If no data is to be sent, the `data
 * @param response - The `response` parameter is a callback function that will be called when the HTTP
 * request is successful and a response is received. It takes one argument, which is the parsed JSON
 * response from the server.
 * @param error - The `error` parameter is a callback function that will be executed if there is an
 * error in the HTTP request. It will receive the error object as its argument.
 */
const simple_post_req = (partial_url, data, response, error) => {

    let args = {
        url: json_url + partial_url,
        method: 'POST',
        headers: {
            'X-WP-Nonce': nonce
        }
    }

    if (data !== null && typeof data === 'object' && Object.keys(data).length > 0) {
        args['data'] = data;
    }

    sendHttpReq(args).then(res => response(JSON.parse(res))).catch(e => error(e));
}


const init = (auction_id, auction_json) => {


    import( /* webpackChunkName: "components/admin/auction/gallery" */ '../components/admin/auction/gallery').then(module => {

        const Gallery = module.Gallery

        new Gallery(
            auction_id,
            simple_post_req,
            auction_data
        )
    })

    
    import( /* webpackChunkName: "components/admin/auction/content" */ '../components/admin/auction/content').then(module => {

        const Content = module.Content

        new Content(
            auction_id,
            auction_json,
            simple_post_req,
            auction_data
        )
    })

    flatpickr("#litepicker", {
        mode: "range",
        minDate: "today",
        dateFormat: "d/m/Y",
    })
}

const is_edit_auction = document.body.classList.contains('page-template-edit-auction');

simple_post_req(
    is_edit_auction ? 'api-edit-auction' : 'api-new-auction',
    is_edit_auction ? { auction_id: new URLSearchParams(window.location.search).get('auction_id') } : {},
    (res) => {
        if ('success' === res.status) {
            init(res.auction_id, res.auction_json)
        } else {
            asta_alert([res.message])
        }
    },
    (e) => console.log(e)
)
