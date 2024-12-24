import JustValidate from 'just-validate';
import {__} from '@wordpress/i18n';

/**
 * Class representing a feedback form.
 */
export default class FeedbackForm {
    constructor() {
        this.feedbackForms = document.querySelectorAll('[data-feedback-form]');
        if (!this.feedbackForms) {
            return false;
        }

        this.feedbackForms.forEach((form) => {
            this.initForm(form);
        });

    }

    /**
     * Initialize the form with validation and submission handling.
     */
    initForm(form) {
        const validator = new JustValidate(form);

        validator
            .addField('[name="first_name"]', [
                {
                    rule: 'required',
                    errorMessage: __('Please fill out this field', 'wlc'),
                },
            ])
            .addField('[name="last_name"]', [
                {
                    rule: 'required',
                    errorMessage: __('Please fill out this field', 'wlc'),
                },
            ])
            .addField('[name="email"]', [
                {
                    rule: 'required',
                    errorMessage: __('Please fill out this field', 'wlc'),
                },
                {
                    rule: 'email',
                    errorMessage: __('Please enter a valid email address', 'wlc'),
                },
            ])
            .addField('[name="subject"]', [
                {
                    rule: 'required',
                    errorMessage: __('Please fill out this field', 'wlc'),
                },
            ])
            .onSuccess(async (event) => {
                const formData = new FormData(form);
                formData.append('action', 'wlc_feedback_form');
                formData.append('nonce', wlc.feedback_form_nonce);

                try {
                    const response = await fetch(wlc.ajax_url, {
                        method: 'POST',
                        body: formData,
                    });
                    const data = await response.json();

                    const existingMessage = form.querySelector('.form-message');
                    if (existingMessage) {
                        existingMessage.remove();
                    }

                    const messageElement = document.createElement('div');
                    messageElement.className = 'form-message';
                    messageElement.innerText = data.data.message;
                    form.appendChild(messageElement);

                    form.reset();

                } catch (error) {
                    console.error(error);
                }
            });
    }

}
