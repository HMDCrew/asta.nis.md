import { asta_alert } from '../utils/asta_alert'
import { sendHttpReq } from './api/http'

const cart = document.querySelector('.cart-menu')

/**
 * The function `add_to_cart` sends a POST request to a JSON URL with a product ID and nonce, updates
 * the number of products in the cart, and displays a success message.
 * @param product_id - The product_id parameter is the ID of the product that you want to add to the
 * cart.
 * @param json_url - The `json_url` parameter is the base URL for the JSON API. It is used to construct
 * the complete URL for the API endpoint.
 * @param nonce - The `nonce` parameter is a security measure used to prevent unauthorized access to
 * certain actions or endpoints in WordPress. It stands for "number used once" and is a unique token
 * generated by WordPress for each user session. In this case, it is used as a header in the HTTP
 * request to authenticate the
 */
export const add_to_cart = (product_id, json_url, nonce) => {

    sendHttpReq({
        url: json_url + 'api-add-to-cart',
        data: {
            product_id: product_id
        },
        headers: { 'X-WP-Nonce': nonce },
        method: 'POST'
    }).then(res => {

        res = JSON.parse(res)

        const n_products = cart.querySelector('.n_products')
        n_products.classList.remove('hide')

        n_products.textContent = res.n_products

        asta_alert([res.message], 'success')

    }).catch(e => {
        console.log(e)
    });
}