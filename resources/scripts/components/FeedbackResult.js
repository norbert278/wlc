import {__} from '@wordpress/i18n';

/**
 * Class representing a feedback form.
 */
export default class FeedbackResult {
    constructor() {
        this.feedbackResults = document.querySelectorAll('[data-feedback-results]');
        if (!this.feedbackResults) return;

        this.feedbackResults.forEach(results => this.initResults(results));
    }

    /**
     * Initialize pagination for feedback results.
     */
    initResults(results) {
        const loadResults = async (page = 1) => {
            const formData = new FormData();
            formData.append('action', 'wlc_feedback_results');
            formData.append('page', page);
            formData.append('nonce', wlc.feedback_results_nonce);
            try {
                const response = await fetch(wlc.ajax_url, {method: 'POST', body: formData});
                const data = await response.json();

                if (data.success) {
                    results.innerHTML = data.data.results.map(result => `
                        <li class="border grid grid-cols-4 p-4" data-id="${result.id}">
                            <p><strong>${__('First Name:', 'wlc')}</strong> ${result.first_name}</p>
                            <p><strong>${__('Last Name:', 'wlc')}</strong> ${result.last_name}</p>
                            <p><strong>${__('Email:', 'wlc')}</strong> ${result.email}</p>
                            <p><strong>${__('Subject:', 'wlc')}</strong> ${result.subject}</p>
                        </li>
                    `).join('');

                    const totalPages = Math.ceil(data.data.total_results / 10);

                    // Add pagination controls
                    if (totalPages > 1) {
                        results.insertAdjacentHTML('beforeend', `
                            <div class="pagination flex justify-center mt-6 gap-4">
                                ${page > 1 ? `<button class="prev-page py-2 px-4 border rounded-md">${__('Previous', 'wlc')}</button>` : ''}
                                ${page < totalPages ? `<button class="next-page py-2 px-4 border rounded-md">${__('Next', 'wlc')}</button>` : ''}
                            </div>
                        `);

                        // Add event listeners for pagination buttons
                        if (page > 1) {
                            results.querySelector('.prev-page').addEventListener('click', () => loadResults(page - 1));
                        }
                        if (page < totalPages) {
                            results.querySelector('.next-page').addEventListener('click', () => loadResults(page + 1));
                        }
                    }

                    // Add event listeners for list items
                    results.querySelectorAll('li').forEach(item => {
                        item.addEventListener('click', () => this.showDetails(item.dataset.id, results));
                    });
                }
            } catch (error) {
                console.error(error);
            }
        };

        loadResults();
    }

    /**
     * Show details of the selected item.
     */
    async showDetails(id, results) {
        const formData = new FormData();
        formData.append('action', 'wlc_feedback_results');
        formData.append('id', id);
        formData.append('nonce', wlc.feedback_results_nonce);

        try {
            const response = await fetch(wlc.ajax_url, {
                method: 'POST',
                body: formData,
            });
            const data = await response.json();

            if (data.success) {
                const detailsContainer = document.querySelector('[data-feedback-details]');
                detailsContainer.innerHTML = `
                    <p class="font-bold text-xl mb-4">${__('Details:', 'wlc')}</p>
                    <div class="border p-4">
                        <p><strong>${__('First Name:', 'wlc')}</strong> ${data.data.result.first_name}</p>
                        <p><strong>${__('Last Name:', 'wlc')}</strong> ${data.data.result.last_name}</p>
                        <p><strong>${__('Email:', 'wlc')}</strong> ${data.data.result.email}</p>
                        <p><strong>${__('Subject:', 'wlc')}</strong> ${data.data.result.subject}</p>
                        <p><strong>${__('Message:', 'wlc')}</strong> ${data.data.result.message}</p>
                        <p><strong>${__('Submitted At:', 'wlc')}</strong> ${data.data.result.created_at}</p>
                    </div>
                `;

                // Scroll to the details section
                results.scrollIntoView({behavior: 'smooth'});
            }
        } catch (error) {
            console.error(error);
        }
    }
}
