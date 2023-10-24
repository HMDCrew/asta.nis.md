import '../../scss/views/register.scss'
import 'vanilla-tilt'
import { sendHttpReq } from '../utils/api/http'
import { asta_alert } from '../utils/asta_alert'
import { check_pwd_validity } from '../utils/passwords';

const tilt_img = document.querySelector(".js-tilt img")
const form = document.querySelector('.register-form')
const submit_btn = document.querySelector('.container-register-form-btn .register-form-btn')

tilt_img && VanillaTilt.init(tilt_img, {
    max: 20,
    speed: 400,
    scale: 1.1,
});

if (form && submit_btn) {

    const email = form.querySelector('input[type="email"]');
    const pwd = form.querySelector('input[name="pwd"]')
    const repeat_pwd = form.querySelector('input[name="repeat_password"]')

    /**
     * The function checks the validity of an email address using a regular expression and adds a
     * success class to the email input if it is valid.
     */
    const check_email_validity = () => {

        const validRegex = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;

        if (email.value.match(validRegex)) {
            email.classList.add('success')
        } else {
            email.classList.remove('success')
        }
    }

    email.addEventListener('keyup', (ev) => check_email_validity(), false)

    /**
     * The function checks the validity of two password inputs and adds appropriate CSS classes based
     * on their match.
     */
    const check_pwd = () => {

        if (check_pwd_validity(pwd.value, repeat_pwd.value)) {
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

    const submin_form = (event) => {
        event.preventDefault();

        const data = {
            user: email.value,
            pwd: pwd.value
        }

        if (email.classList.contains('success') && pwd.classList.contains('success')) {

            sendHttpReq({
                url: register_data.json_url + 'api-register',
                data: data
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {
                    asta_alert(['Register gistration complete'], 'success')
                    window.location = '/login'
                } else {
                    asta_alert([res.message])
                }

            }).catch(e => {
                console.log(e);
            });

        } else {
            asta_alert(['<strong>Error:</strong> check fields.'])
        }

    }
    submit_btn.addEventListener('click', submin_form, false)
}