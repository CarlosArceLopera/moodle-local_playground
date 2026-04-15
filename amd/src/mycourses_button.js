/**
 * Moves the playground button into the My Courses header action area.
 * @module local_playground/mycourses_button
 */

export const init = () => {
    const playgroundButton = document.getElementById('local-playground-mycourses-button');
    if (!playgroundButton) {
        return;
    }

    // Prefer the my-action-buttons-right container (standard Moodle My Courses layout).
    const actionButtonsRight = document.querySelector('.my-action-buttons.my-action-buttons-right');
    if (actionButtonsRight) {
        actionButtonsRight.appendChild(playgroundButton);
        return;
    }

    // Fallback: header-actions-container.
    const headerActions = document.querySelector('.header-actions-container');
    if (headerActions) {
        headerActions.appendChild(playgroundButton);
    }
};
