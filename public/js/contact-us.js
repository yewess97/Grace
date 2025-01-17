'use strict';

$(document).ready(() => {

    $(document).on('submit', '#contact_us_form', (e) => {

        const name = $('#name'),
            email = $('#email'),
            message = $('#message');

        const emailRegex = /^\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,3}$/,
            nameRegex = /^[a-zA-Z ,.'-]+$/;

        const errorName = $('#name_error'),
            errorEmail = $('#email_error'),
            errorMessage = $('#message_error');

        if (name && email && message) {

            let errorNameMsg = '',
                errorEmailMsg = '',
                errorMessageMsg = '';

            /* ======= Name Error ======= */
            if (!name.val()) {
                errorNameMsg = 'Name is required';
            } else if (nameRegex && !nameRegex.test(name.val())) {
                errorNameMsg = 'Name must be only characters';
            } else if (name.val().length < 2) {
                errorNameMsg = 'Name must be at least 2 characters';
            } else if (name.val().length > 50) {
                errorNameMsg = 'Name must be less than 50 characters';
            }

            /* ======= Email Error ======= */
            if (!email.val()) {
                errorEmailMsg = 'E-mail is required';
            } else if (emailRegex && !emailRegex.test(email.val())) {
                errorEmailMsg = 'Invalid E-mail Format';
            }

            /* ======= Message Error ======= */
            if (!message.val()) {
                errorMessageMsg = 'Message is required';
            } else if (message.val().length < 2) {
                errorMessageMsg = 'Message must be at least 2 characters';
            }

            /* ======= Display Error Messages ======= */
            if (errorNameMsg || errorEmailMsg || errorMessageMsg) {
                e.preventDefault();

                /* ======= Clear Error Messages ======= */
                errorName.empty();
                errorEmail.empty();
                errorMessage.empty();

                /* ======= Name ValidationHelper ======= */
                if (errorNameMsg) {
                    errorName.parent().css({'display': 'block', 'margin-top': '1rem'});
                    errorName.append(`<li>${errorNameMsg}</li>`);
                } else {
                    errorName.parent().css('display', 'none');
                }

                /* ======= Email ValidationHelper ======= */
                if (errorEmailMsg) {
                    errorEmail.parent().css({'display': 'block', 'margin-top': '1rem'});
                    errorEmail.append(`<li>${errorEmailMsg}</li>`);
                } else {
                    errorEmail.parent().css('display', 'none');
                }

                /* ======= Message ValidationHelper ======= */
                if (errorMessageMsg) {
                    errorMessage.parent().css({'display': 'block', 'margin-top': '1rem'});
                    errorMessage.append(`<li>${errorMessageMsg}</li>`);
                } else {
                    errorMessage.parent().css('display', 'none');
                }

            } else {
                e.preventDefault();

                /* ======= Send Email ======= */

                let params = {
                    name: name.val(),
                    email: email.val(),
                    message: message.val(),
                };

                const serviceID = 'service_ri1slgn',
                    templateID = 'template_5xiszts';

                emailjs.send(serviceID, templateID, params)
                    .then((res) => {
                        Swal.fire({
                            title: 'Success!',
                            html: `<h2 class="fs-5">Thank you ${name.val()}</h2><h3 class="mt-2 fs-6">You message has been sent successfully to Yousif Ayman (The Admin)!</h3>`,
                            icon: 'success',
                            showConfirmButton: true,
                        });
                        $(e.target)[0].reset();
                        errorName.empty();
                        errorEmail.empty();
                        errorMessage.empty();
                        $('.form-notch-middle').css('border-top', '');
                    })
                    .catch((err) => {
                        Swal.fire({
                            title: 'Error!',
                            html: `<h2 class="fs-5">Sorry ${name.val()}</h2><h3 class="mt-2 fs-6">Something went wrong, please try again later!</h3>`,
                            icon: 'error',
                            showConfirmButton: true,
                        });
                    });

            }
        }
    });
});
