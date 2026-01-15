/**
 * Initialize step1 functionality
 * @module local_playground/step1
 * @param {string} sesskey - The Moodle session key for AJAX requests
 */

export const init = (sesskey) => {
    // eslint-disable-next-line no-console
    console.log('Step1 init called with sesskey');
// rebuild and grunt
    const nextButton = document.getElementById('next');
    const playgroundTitle = document.getElementById('playgroundtitle');

    // eslint-disable-next-line no-console
    console.log('Next button:', nextButton);
    // eslint-disable-next-line no-console
    console.log('Playground title input:', playgroundTitle);

    if (!nextButton || !playgroundTitle) {
        // eslint-disable-next-line no-console
        console.error('Required elements not found!');
        return;
    }

    // eslint-disable-next-line no-console
    console.log('Adding click event listener');
    console.log('Adding click event listener2');
    nextButton.addEventListener('click', () => {
        // eslint-disable-next-line no-console
        console.log('Next button clicked!');

        nextButton.textContent = "Creating your playground course";
        nextButton.classList.add("animatedellipsis");

        const title = encodeURIComponent(playgroundTitle.value);
        const url = `ajax.php?action=create_playground_course&title=${title}&sesskey=${sesskey}`;

        // eslint-disable-next-line no-console
        console.log('Fetching URL:', url);

        fetch(url)
            .then(response => {
                // eslint-disable-next-line no-console
                console.log('Response received:', response);
                // Parse JSON even if response is not ok to get error message
                return response.json().then(data => {
                    if (!response.ok) {
                        throw new Error(data.error || `HTTP error ${response.status}`);
                    }
                    return data;
                });
            })
            .then(resultData => {
                // eslint-disable-next-line no-console
                console.log('Result data:', resultData);
                if (resultData.success && resultData.url) {
                    window.location.href = resultData.url;
                } else {
                    throw new Error(resultData.error || 'Unknown error occurred');
                }
            })
            .catch(error => {
                // eslint-disable-next-line no-console
                console.error('Error creating playground course:', error);
                nextButton.textContent = "Error - Please try again";
                nextButton.classList.remove("animatedellipsis");
                alert('Error creating playground course: ' + error.message);
            });
    });
};
