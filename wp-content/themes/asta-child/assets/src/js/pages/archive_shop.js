import 'toolcool-range-slider/dist/plugins/tcrs-binding-labels.min.js';
import 'toolcool-range-slider';

import '../../scss/views/archive_shop.scss'
import { add_to_cart } from '../utils/add_to_cart'

const { json_url, nonce } = shop_data

const adds_to_cart = document.querySelectorAll('.add-to-cart')

const handle_add_to_cart = (btn) => {

    const product_id = btn.getAttribute('product_id')

    add_to_cart(product_id, json_url, nonce)
}

adds_to_cart.forEach(async btn => btn.addEventListener('click', ev => handle_add_to_cart(btn), false))

import( /* webpackChunkName: "components/filter-bar" */ '../components/filter-bar').then(module => {

    const FilterBar = module.FilterBar
    const LoadMore = module.LoadMore

    new FilterBar(
        json_url + 'api-get-auctions',
        handle_add_to_cart,
        auctions_data
    )

    new LoadMore(
        json_url + 'api-get-auctions',
        handle_add_to_cart,
        auctions_data
    )
})
