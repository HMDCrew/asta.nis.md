import '../../scss/views/login.scss'
import 'vanilla-tilt'
import { sendHttpReq } from '../utils/api/http';
import { asta_alert } from '../utils/asta_alert';

const tilt_img = document.querySelector(".js-tilt img")
const form = document.querySelector('.login-form')
const submit_btn = document.querySelector('.container-login-form-btn .login-form-btn')

tilt_img && VanillaTilt.init(tilt_img, {
    max: 20,
    speed: 400,
    scale: 1.1,
});


if (form && submit_btn) {

    const email_input = form.querySelector('input[type="email"]')
    const password_input = form.querySelector('input[type="password"]')
    const remember = form.querySelector('.login-remember input[type="checkbox"]')
    const redirect_to = form.querySelector('input[name="redirect_to"]')


    /**
     * This is a function that submits a login form using AJAX and displays an error message if the
     * login fails.
     * @param event - The event parameter is an object that represents the event that triggered the
     * function. In this case, it is likely a form submission event. The function uses the
     * preventDefault() method to prevent the default behavior of the form submission event, which is
     * to reload the page.
     */
    const submin_form = (event) => {
        event.preventDefault();

        const data = {
            user: email_input.value,
            pwd: password_input.value,
            remember: remember.checked
        }

        sendHttpReq({
            url: login_data.json_url + 'api-login',
            data: data
        }).then(res => {

            res = JSON.parse(res)

            if ('success' === res.status) {
                window.location.replace(redirect_to.value)
            } else {
                asta_alert([res.message])
            }

        }).catch(e => {
            console.log(e);
        });
    }

    submit_btn.addEventListener('click', submin_form)
}
