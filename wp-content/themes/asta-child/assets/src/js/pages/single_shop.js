import { add_to_cart } from '../utils/add_to_cart'
import 'swiper/css'
import 'swiper/css/navigation'
import '@fancyapps/ui/dist/fancybox/fancybox.css'

import '../../scss/views/single_shop.scss'

const { product_id, json_url, nonce } = shop_data

import( /* webpackChunkName: "components/gallery" */ '../components/gallery').then(module => {

    const Gallery = module.Gallery

    new Gallery()
})

const buy_now = document.querySelector('.sidebar .buy-now')

buy_now.addEventListener('click', ev => add_to_cart(product_id, json_url, nonce), false)
