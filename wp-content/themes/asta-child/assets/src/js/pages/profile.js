import '../../scss/views/profile.scss'
import { sendHttpReq } from '../utils/api/http'
import { asta_alert } from '../utils/asta_alert'

const profile_form = document.querySelector('.user-profile')


import( /* webpackChunkName: "components/profile/vendor-banner" */ '../components/profile/vendor-banner').then(module => {

    const ProfileVendorBanner = module.ProfileVendorBanner

    new ProfileVendorBanner(profile_form, sendHttpReq, asta_alert, profile_data)
})


import( /* webpackChunkName: "components/profile/upload_foto_profile" */ '../components/profile/upload_foto_profile').then(module => {

    const ProfilePicture = module.ProfilePicture

    new ProfilePicture(profile_form, profile_data)
})


import( /* webpackChunkName: "components/profile/profile-details" */ '../components/profile/profile-details').then(module => {

    const ProfileDetails = module.ProfileDetails

    new ProfileDetails(profile_form, sendHttpReq, asta_alert, profile_data)
})


import( /* webpackChunkName: "components/profile/credit-cards" */ '../components/profile/credit-cards').then(module => {

    const ProfilePaymentCards = module.ProfilePaymentCards

    new ProfilePaymentCards(profile_form, sendHttpReq, asta_alert, profile_data)
})


import( /* webpackChunkName: "components/profile/profile-iban" */ '../components/profile/profile-iban').then(module => {

    const ProfileIban = module.ProfileIban

    new ProfileIban(profile_form, sendHttpReq, asta_alert, profile_data)
})
