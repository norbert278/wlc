import domReady from '@roots/sage/client/dom-ready';
import FeedbackForm from "@scripts/components/FeedbackForm.js";

/**
 * Application entrypoint
 */
domReady(async () => {
    const feedbackForm = new FeedbackForm();
});

/**
 * @see {@link https://webpack.js.org/api/hot-module-replacement/}
 */
import.meta.webpackHot?.accept(console.error);
