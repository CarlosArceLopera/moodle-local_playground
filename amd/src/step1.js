/**
 * Initialize step1 functionality
 * @module local_playground/step1
 * @param {string} sesskey - The Moodle session key for AJAX requests
 */

import Notification from 'core/notification';

export const init = (sesskey) => {
    const nextButton = document.getElementById('next');
    const playgroundTitle = document.getElementById('playgroundtitle');

    if (!nextButton || !playgroundTitle) {
        return;
    }

    nextButton.addEventListener('click', () => {
        nextButton.textContent = "Creating your playground course";
        nextButton.classList.add("animatedellipsis");

        const title = encodeURIComponent(playgroundTitle.value);
        const url = `ajax.php?action=create_playground_course&title=${title}&sesskey=${sesskey}`;

        fetch(url)
            .then(response => response.json().then(data => ({ok: response.ok, data})))
            .then(({ok, data}) => {
                if (!ok) {
                    throw new Error(data.error || `HTTP error`);
                }
                if (data.success && data.url) {
                    window.location.href = data.url;
                    return;
                }
                throw new Error(data.error || 'Unknown error occurred');
            })
            .catch(error => {
                nextButton.textContent = "Error - Please try again";
                nextButton.classList.remove("animatedellipsis");
                Notification.exception(error);
            });
    });
};
