const loadingState = {
    overlayVisible: false,
};

function getOverlay() {
    return document.getElementById('page-loading-overlay');
}

function showPageSkeleton() {
    const overlay = getOverlay();
    if (!overlay) {
        return;
    }

    loadingState.overlayVisible = true;
    document.body.classList.add('is-page-loading');
    overlay.setAttribute('aria-hidden', 'false');
}

function hidePageSkeleton() {
    const overlay = getOverlay();
    if (!overlay) {
        return;
    }

    loadingState.overlayVisible = false;
    document.body.classList.remove('is-page-loading');
    overlay.setAttribute('aria-hidden', 'true');
}

function setButtonLoading(button, options = {}) {
    if (!button || button.dataset.loadingState === 'true') {
        return button;
    }

    const label = options.label || button.getAttribute('aria-label') || button.textContent.trim();

    button.dataset.originalDisabled = button.disabled ? 'true' : 'false';
    button.dataset.loadingState = 'true';
    button.dataset.loadingLabel = label;
    button.classList.add('btn-loading');
    button.setAttribute('aria-busy', 'true');
    button.setAttribute('aria-label', `${label} sedang diproses`);
    button.disabled = true;

    return button;
}

function clearButtonLoading(button) {
    if (!button) {
        return button;
    }

    button.classList.remove('btn-loading');
    button.removeAttribute('aria-busy');
    if (button.dataset.loadingLabel) {
        button.setAttribute('aria-label', button.dataset.loadingLabel);
    }
    delete button.dataset.loadingState;
    delete button.dataset.loadingLabel;
    button.disabled = button.dataset.originalDisabled === 'true';
    delete button.dataset.originalDisabled;

    return button;
}

function isSameOriginNavigation(anchor) {
    if (!anchor || !anchor.href) {
        return false;
    }

    if (anchor.target && anchor.target !== '_self') {
        return false;
    }

    if (anchor.hasAttribute('download')) {
        return false;
    }

    const href = anchor.getAttribute('href') || '';
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) {
        return false;
    }

    if (href.startsWith('mailto:') || href.startsWith('tel:')) {
        return false;
    }

    try {
        return new URL(anchor.href, window.location.href).origin === window.location.origin;
    } catch {
        return false;
    }
}

function prepareNavigation() {
    showPageSkeleton();
}

document.addEventListener('DOMContentLoaded', () => {
    hidePageSkeleton();

    document.addEventListener('click', (event) => {
        const anchor = event.target.closest?.('a[href]');
        if (!anchor || anchor.closest('[data-no-page-loading="true"]')) {
            return;
        }

        if (!isSameOriginNavigation(anchor)) {
            return;
        }

        const href = new URL(anchor.href, window.location.href);
        if (href.href === window.location.href) {
            return;
        }

        if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
            return;
        }

        event.preventDefault();
        prepareNavigation();

        window.setTimeout(() => {
            window.location.href = href.href;
        }, 30);
    }, true);

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noLoading === 'true') {
            return;
        }

        const submitter = event.submitter instanceof HTMLElement ? event.submitter : form.querySelector('button[type="submit"], input[type="submit"]');
        if (submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement) {
            setButtonLoading(submitter);
        }

        if (form.dataset.pageLoading !== 'false') {
            showPageSkeleton();
        }
    }, true);
});

window.addEventListener('load', hidePageSkeleton);
window.addEventListener('pageshow', hidePageSkeleton);
window.addEventListener('pagehide', () => {
    loadingState.overlayVisible = false;
});

window.BakmiLoading = {
    clearButtonLoading,
    hidePageSkeleton,
    prepareNavigation,
    setButtonLoading,
    showPageSkeleton,
};
