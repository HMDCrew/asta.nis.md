import 'swiper/css';
import 'swiper/css/navigation';

import '../../scss/views/new_product.scss'
import { sendHttpReq } from '../utils/api/http'
import { asta_alert } from '../utils/asta_alert'

const { json_url, nonce } = product_data;

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


const init = async (product_id, product_json) => {

    await import(
        /* webpackPrefetch: true */
        /* webpackChunkName: "components/admin/product/gallery" */
        '../components/admin/product/gallery.js'
    ).then(module => {

        const Gallery = module.Gallery

        new Gallery(
            product_id,
            simple_post_req,
            product_data
        )
    })

    
    await import(
        /* webpackPrefetch: true */
        /* webpackChunkName: "components/admin/product/content" */
        '../components/admin/product/content.js'
    ).then(module => {

        const Content = module.Content

        new Content(
            product_id,
            product_json,
            simple_post_req,
            product_data
        )
    })

}

const is_edit_product = document.body.classList.contains('page-template-edit-product');

simple_post_req(
    is_edit_product ? 'api-edit-product' : 'api-new-product',
    is_edit_product ? { product_id: new URLSearchParams(window.location.search).get('product_id') } : {},
    (res) => {
        console.log(res)
        if ('success' === res.status) {
            init(res.product_id, res.product_json)
        } else {
            asta_alert([res.message])
        }
    },
    (e) => console.log(e)
);
