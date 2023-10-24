import 'flatpickr/dist/themes/dark.css'
import 'toolcool-range-slider/dist/plugins/tcrs-binding-labels.min.js';
import 'toolcool-range-slider';

import '../../scss/views/archive_auctions.scss'

const { json_url } = auctions_data

import( /* webpackChunkName: "components/filter-bar" */ '../components/filter-bar').then(module => {

    const FilterBar = module.FilterBar
    const LoadMore = module.LoadMore

    new FilterBar(
        json_url + 'api-get-auctions',
        null,
        auctions_data
    )

    new LoadMore(
        json_url + 'api-get-auctions',
        null,
        auctions_data
    )
})
