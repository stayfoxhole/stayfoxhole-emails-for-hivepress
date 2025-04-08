const sfxhEmailsSuccessDiv = (msg) => {
    const div = document.createElement('div');
    div.classList.add('sfxhemails-success', 'sfxhemails-response');
    div.textContent = msg;
    div.style.backgroundColor = '#f0fff4';
    div.style.color = '#155724';
    div.style.border = '1px solid #c3e6cb';
    div.style.padding = '12px';
    div.style.marginTop = '10px';
    div.style.borderRadius = '5px';
    return div;
};

const sfxhEmailsErrorDiv = (msg) => {
    const div = document.createElement('div');
    div.classList.add('sfxhemails-error', 'sfxhemails-response');
    div.textContent = msg;
    div.style.backgroundColor = '#fff5f5';
    div.style.color = '#721c24';
    div.style.border = '1px solid #e3342f'; // WordPress red
    div.style.padding = '12px';
    div.style.marginTop = '10px';
    div.style.borderRadius = '5px';
    return div;
};

const initSfxhSendTestEmail = () => {
    const sendTestButton = document.querySelector(`[data-class="sfxh-send-test-email"]`);

    if (sendTestButton) {
        sendTestButton.addEventListener('click', (e) => {
            e.preventDefault();

            // Remove existing response messages
            const existingResponses = document.querySelectorAll('.sfxhemails-response');
            existingResponses.forEach(el => el.remove());

            if (window.sfxhEmails && sfxhEmails.restUrl && sfxhEmails.restNonce) {
                sfxhSendTest().then((response) => {
                    console.log('Test email response:', response);

                    const msg = response.success
                        ? 'Test email sent successfully.'
                        : (response.error || 'Something went wrong.');

                    const resultDiv = response.success
                        ? sfxhEmailsSuccessDiv(msg)
                        : sfxhEmailsErrorDiv(msg);

                    sendTestButton.insertAdjacentElement('afterend', resultDiv);
                }).catch((error) => {
                    console.error('Test email failed:', error);

                    const errorDiv = sfxhEmailsErrorDiv('Failed to send test email.');
                    sendTestButton.insertAdjacentElement('afterend', errorDiv);
                });
            }
        });
    }
};

const sfxhSendTest = async () => {
    const url = window.sfxhEmails.restUrl + 'foxhole/v1/test-email';
    const recipient = document.querySelector('[name="hp_recipient"]');
    const booking_id = document.querySelector('[name="hp_booking_id"]');
    const postName = document.querySelector('#post_name');

    if (recipient && postName) {
        const templateName = postName.value;

        if (templateName) {
            const data = {
                recipient: recipient.value,
                template: templateName,
                booking_id: booking_id.value,
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': sfxhEmails.restNonce,
                },
                body: JSON.stringify(data),
            });

            return await response.json();
        }
    }

    return { error: 'Missing recipient or template' };
};

const makeAlert = (msg) => {
    alert(msg) 
}


(function() {
    initSfxhSendTestEmail();
})();
