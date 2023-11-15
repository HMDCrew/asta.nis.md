import '../../scss/views/profile.scss'
import { sendHttpReq } from '../utils/api/http'
import { sendHttpForm } from '../utils/api/form'
import { check_pwd_validity } from '../utils/passwords';
import { asta_alert } from '../utils/asta_alert'

const profile_form = document.querySelector('.user-profile')
const { nonce, json_url, stripe_pk } = profile_data


if (profile_form) {

    const photo_profile = profile_form.querySelector('input[type="file"]');

    const upload_picture = () => {

        if (photo_profile.files.length > 0) {

            const container = photo_profile.parentNode;

            // loading
            container.classList.add('loading')

            let formData = new FormData();
            formData.append("file", photo_profile.files[0]);

            sendHttpForm({
                url: json_url + 'api-profile-upload-image',
                data: formData,
                headers: {
                    'X-WP-Nonce': nonce
                }
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {
                    container.querySelector('img').setAttribute('src', res.image)
                } else {
                    console.log(res)
                }

                // end loading 
                container.classList.remove('loading')

            }).catch(e => {
                console.log(e);
                // end loading
                container.classList.remove('loading')
            });
        }
    }
    photo_profile.addEventListener('change', (ev) => upload_picture(), false);


    const btn_update_profile = profile_form.querySelector('.update-profile')
    const pwd = profile_form.querySelector('input[name="password"]');
    const repeat_pwd = profile_form.querySelector('input[name="repeat-password"]');

    /**
     * The function checks the validity of two password inputs and adds appropriate CSS classes based
     * on their match.
     */
    const check_pwd = () => {

        if (
            'password' !== repeat_pwd.value && 'password' !== pwd.value &&
            check_pwd_validity(pwd.value, repeat_pwd.value)
        ) {
            pwd.classList.remove('error')
            repeat_pwd.classList.remove('error')
            pwd.classList.add('success')
            repeat_pwd.classList.add('success')
        } else {
            pwd.classList.remove('success')
            repeat_pwd.classList.remove('success')
            pwd.classList.add('error')
            repeat_pwd.classList.add('error')
        }
    }

    pwd.addEventListener('keyup', (ev) => check_pwd(), false)
    repeat_pwd.addEventListener('keyup', (ev) => check_pwd(), false)

    const update_profile_info = (event) => {
        event.preventDefault();

        const first_name = profile_form.querySelector('input[name="first-name"]');
        const last_name = profile_form.querySelector('input[name="last-name"]');
        const website = profile_form.querySelector('input[name="website"]');
        const email = profile_form.querySelector('input[name="email"]');
        const password = profile_form.querySelector('input[name="password"]');
        const repeat_password = profile_form.querySelector('input[name="repeat-password"]');
        const description = profile_form.querySelector('textarea[name="description"]');

        let data = {
            first_name: first_name.value,
            last_name: last_name.value,
            website: website.value,
            email: email.value,
            description: description.value,
        };

        if (
            'password' !== repeat_pwd.value && 'password' !== pwd.value &&
            check_pwd_validity(password.value, repeat_password.value)
        ) {
            data['password'] = password.value;
            data['repeat_password'] = repeat_password.value;
        }

        sendHttpReq({
            url: json_url + 'api-profile-update-info',
            data: data,
            method: 'POST',
            headers: {
                'X-WP-Nonce': nonce
            }
        }).then(res => {

            res = JSON.parse(res)

            if ('success' === res.status) {
                asta_alert([res.message], 'success')
            } else {
                asta_alert([res.message])
            }

        }).catch(e => {
            console.log(e);
        });
    }

    btn_update_profile.addEventListener('click', (ev) => update_profile_info(ev), false);


    /**
     * User Cards
     */
    const payment_form = profile_form.querySelector('#payment-form')
    const pay_submit = payment_form?.querySelector('#submit')
    const carte_card = profile_form.querySelector('.create-cart')
    const carte_errors = document.getElementById('card-errors')

    const render_new_card = () => {
        
    }

    const hundle_add_card = (stripe, card_element) => {

        stripe.createToken(card_element).then((result) => {

            if (result.error) {
                carte_errors.textContent = result.error.message;
            } else {
                sendHttpReq({
                    url: json_url + 'api-card-to-user',
                    method: 'POST',
                    data: {
                        token: result.token.id
                    },
                    headers: { 'X-WP-Nonce': nonce }
                }).then(async res => {

                    res = JSON.parse(res)

                    console.log(res);

                }).catch(e => {
                    console.log(e)
                })
            }
        });
    }

    const build_card_form = async (pk) => {
    
        const stripe = Stripe(pk, { apiVersion: '2020-08-27' })
    
        const elements = stripe.elements();
        const card_element = elements.create('card');
        card_element.mount('#card-element');

        payment_form.parentNode.classList.remove('d-none')

        pay_submit.addEventListener('click', ev => hundle_add_card(stripe, card_element), false)
    }
    
    carte_card.addEventListener('click', ev => build_card_form(stripe_pk), false)
}
