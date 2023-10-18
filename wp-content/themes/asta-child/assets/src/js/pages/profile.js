import '../../scss/views/profile.scss'
import { sendHttpReq, sendHttpForm } from '../utils/api';
import { check_pwd_validity } from '../utils/passwords';
import { wpr_alert } from '../utils/helpers';

const profile_form = document.querySelector('.user-profile')

if (profile_form) {

    const photo_profile = profile_form.querySelector('.user-profile input[type="file"]');
    const btn_update_profile = profile_form.querySelector('.update-profile')

    const upload_picture = () => {

        if (photo_profile.files.length > 0) {

            const container = photo_profile.parentNode;

            // loading
            container.classList.add('loading')

            let formData = new FormData();
            formData.append("file", photo_profile.files[0]);

            sendHttpForm({
                url: profile_data.json_url + 'api-profile-upload-image',
                data: formData,
                headers: {
                    'X-WP-Nonce': profile_data.nonce
                }
            }).then(res => {

                res = JSON.parse(res)

                if ('success' === res.status) {
                    container.querySelector('img').setAttribute('src', res.image)
                    console.log(res)
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
            url: profile_data.json_url + 'api-profile-update-info',
            data: data,
            method: 'POST',
            headers: {
                'X-WP-Nonce': profile_data.nonce
            }
        }).then(res => {

            res = JSON.parse(res)

            if ('success' === res.status) {
                wpr_alert([res.message], 'success')
            } else {
                wpr_alert([res.message])
            }

        }).catch(e => {
            console.log(e);
        });
    }
    btn_update_profile.addEventListener('click', (ev) => update_profile_info(ev), false);
}
