/**
 * Initialize step1 functionality
 * @module local_playground/step1
 */

export const init = () => {
    // eslint-disable-next-line no-console
    console.log('Step1 init called');

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

    nextButton.addEventListener('click', () => {
        // eslint-disable-next-line no-console
        console.log('Next button clicked!');

        nextButton.textContent = "Creating your playground course";
        nextButton.classList.add("animatedellipsis");

        const title = encodeURIComponent(playgroundTitle.value);
        const url = `ajax.php?action=create_playground_course&title=${title}`;

        // eslint-disable-next-line no-console
        console.log('Fetching URL:', url);

        fetch(url)
            .then(response => {
                // eslint-disable-next-line no-console
                console.log('Response received:', response);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(resultData => {
                // eslint-disable-next-line no-console
                console.log('Result data:', resultData);
                window.location.href = resultData;
            })
            .catch(error => {
                // eslint-disable-next-line no-console
                console.error('Error creating playground course:', error);
                nextButton.textContent = "Error - Please try again";
                nextButton.classList.remove("animatedellipsis");
            });
    });
};
